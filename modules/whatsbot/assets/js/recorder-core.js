
(function (factory) {
    var browser = typeof window == "object" && !!window.document;
    var win = browser ? window : Object;
    factory(win, browser);
    if (typeof (define) == 'function' && define.amd) {
        define(function () {
            return win.Recorder;
        });
    };
    if (typeof (module) == 'object' && module.exports) {
        module.exports = win.Recorder;
    };
}(function (Export, isBrowser) {
    "use strict";

    var NOOP = function () { };
    var IsNum = function (v) { return typeof v == "number" };

    var Recorder = function (set) {
        return new initFn(set);
    };
    var LM = Recorder.LM = "2024-04-09 19:15";
    var GitUrl = "https://github.com/xiangyuecn/Recorder";
    var RecTxt = "Recorder";
    var getUserMediaTxt = "getUserMedia";
    var srcSampleRateTxt = "srcSampleRate";
    var sampleRateTxt = "sampleRate";
    var bitRateTxt = "bitRate";
    var CatchTxt = "catch";

    var WRec2 = Export[RecTxt];
    if (WRec2 && WRec2.LM == LM) {
        WRec2.CLog(WRec2.i18n.$T("K8zP::重复导入{1}", 0, RecTxt), 3);
        return;
    };
    Recorder.IsOpen = function () {
        var stream = Recorder.Stream;
        if (stream) {
            var tracks = stream.getTracks && stream.getTracks() || stream.audioTracks || [];
            var track = tracks[0];
            if (track) {
                var state = track.readyState;
                return state == "live" || state == track.LIVE;
            };
        };
        return false;
    };

    Recorder.BufferSize = 4096;
    Recorder.Destroy = function () {
        CLog(RecTxt + " Destroy");
        Disconnect();

        for (var k in DestroyList) {
            DestroyList[k]();
        };
    };
    var DestroyList = {};
    Recorder.BindDestroy = function (key, call) {
        DestroyList[key] = call;
    };
    Recorder.Support = function () {
        if (!isBrowser) return false;
        var scope = navigator.mediaDevices || {};
        if (!scope[getUserMediaTxt]) {
            scope = navigator;
            scope[getUserMediaTxt] || (scope[getUserMediaTxt] = scope.webkitGetUserMedia || scope.mozGetUserMedia || scope.msGetUserMedia);
        };
        if (!scope[getUserMediaTxt]) {
            return false;
        };
        Recorder.Scope = scope;

        if (!Recorder.GetContext()) {
            return false;
        };
        return true;
    };
    Recorder.GetContext = function (tryNew) {
        if (!isBrowser) return null;
        var AC = window.AudioContext;
        if (!AC) {
            AC = window.webkitAudioContext;
        };
        if (!AC) {
            return null;
        };

        var ctx = Recorder.Ctx;
        if (!ctx || ctx.state == "closed") {
            ctx = Recorder.Ctx = new AC();
            Recorder.NewCtxs = Recorder.NewCtxs || [];

            Recorder.BindDestroy("Ctx", function () {
                var ctx = Recorder.Ctx;
                if (ctx && ctx.close) {//能关掉就关掉，关不掉就保留着
                    CloseCtx(ctx);
                    Recorder.Ctx = 0;
                };
                var arr = Recorder.NewCtxs; Recorder.NewCtxs = [];
                for (var i = 0; i < arr.length; i++)CloseCtx(arr[i]);
            });
        };
        if (tryNew && ctx.close) {//没法关闭的不允许再创建
            try {
                ctx = new AC();
                Recorder.NewCtxs.push(ctx);
            } catch (e) {
                CLog("GetContext tryNew Error", 1, e);
            }
        };
        return ctx;
    };
    Recorder.CloseNewCtx = function (ctx) {
        if (ctx && ctx != Recorder.Ctx) {
            CloseCtx(ctx);
            var arr = Recorder.NewCtxs || [], L = arr.length;
            for (var i = 0; i < arr.length; i++) {
                if (arr[i] == ctx) { arr.splice(i, 1); break; }
            }
            CLog($T("mSxV::剩{1}个GetContext未close", 0, L + "-1=" + arr.length), arr.length ? 3 : 0);
        }
    };
    var CloseCtx = function (ctx) {
        if (ctx && ctx.close) {
            ctx._isC = 1;
            try { ctx.close() } catch (e) { CLog("ctx close err", 1, e) }
        }
    };
    var ResumeCtx = Recorder.ResumeCtx = function (ctx, check, True, False) {
        var isEnd = 0, isBind = 0, isLsSC = 0, runC = 0, EL = "EventListener", Tag = "ResumeCtx ";
        var end = function (err, ok) {
            if (isBind) { bind() }
            if (!isEnd) {
                isEnd = 1;
                err && False(err, runC);
                ok && True(runC);
            }
            if (ok) {
                if (!ctx._LsSC && ctx["add" + EL]) ctx["add" + EL]("statechange", run);
                ctx._LsSC = 1; isLsSC = 1;
            }
        };
        var bind = function (add) {
            if (add && isBind) return; isBind = add ? 1 : 0;
            var types = ["focus", "mousedown", "mouseup", "touchstart", "touchend"];
            for (var i = 0; i < types.length; i++)
                window[(add ? "add" : "remove") + EL](types[i], run, true);
        };
        var run = function () {
            var sVal = ctx.state, spEnd = CtxSpEnd(sVal);
            if (!isEnd && !check(spEnd ? ++runC : runC)) return end();
            if (spEnd) {
                if (isLsSC) CLog(Tag + "sc " + sVal, 3);
                bind(1);
                ctx.resume().then(function () {
                    if (isLsSC) CLog(Tag + "sc " + ctx.state);
                    end(0, 1);
                })[CatchTxt](function (e) {
                    CLog(Tag + "error", 1, e);
                    if (!CtxSpEnd(ctx.state)) {
                        end(e.message || "error");
                    }
                });
            } else if (sVal == "closed") {
                if (isLsSC && !ctx._isC) CLog(Tag + "sc " + sVal, 1);
                end("ctx closed");
            } else { end(0, 1) };
        };
        run();
    };
    var CtxSpEnd = Recorder.CtxSpEnd = function (v) {
        return v == "suspended" || v == "interrupted";
    };
    var CtxState = function (ctx) {
        var v = ctx.state, msg = "ctx.state=" + v;
        if (CtxSpEnd(v)) msg += $T("nMIy::（注意：ctx不是running状态，rec.open和start至少要有一个在用户操作(触摸、点击等)时进行调用，否则将在rec.start时尝试进行ctx.resume，可能会产生兼容性问题(仅iOS)，请参阅文档中runningContext配置）");
        return msg;
    };



    var ConnectEnableWebM = "ConnectEnableWebM";
    Recorder[ConnectEnableWebM] = true;


    var ConnectEnableWorklet = "ConnectEnableWorklet";
    Recorder[ConnectEnableWorklet] = false;


    var Connect = function (streamStore, isUserMedia) {
        var bufferSize = streamStore.BufferSize || Recorder.BufferSize;

        var stream = streamStore.Stream;
        var ctx = stream._RC || stream._c || Recorder.GetContext(true);
        stream._c = ctx;

        var mediaConn = function (node) {
            var media = stream._m = ctx.createMediaStreamSource(stream);
            var ctxDest = ctx.destination, cmsdTxt = "createMediaStreamDestination";
            if (ctx[cmsdTxt]) {
                ctxDest = stream._d = ctx[cmsdTxt]();
            };
            media.connect(node);
            node.connect(ctxDest);
        }
        var isWebM, isWorklet, badInt, webMTips = "";
        var calls = stream._call;
        var onReceive = function (float32Arr) {
            for (var k0 in calls) {//has item
                var size = float32Arr.length;

                var pcm = new Int16Array(size);
                var sum = 0;
                for (var j = 0; j < size; j++) {//floatTo16BitPCM 
                    var s = Math.max(-1, Math.min(1, float32Arr[j]));
                    s = s < 0 ? s * 0x8000 : s * 0x7FFF;
                    pcm[j] = s;
                    sum += Math.abs(s);
                };

                for (var k in calls) {
                    calls[k](pcm, sum);
                };

                return;
            };
        };

        var scriptProcessor = "ScriptProcessor";
        var audioWorklet = "audioWorklet";
        var recAudioWorklet = RecTxt + " " + audioWorklet;
        var RecProc = "RecProc";
        var MediaRecorderTxt = "MediaRecorder";
        var MRWebMPCM = MediaRecorderTxt + ".WebM.PCM";
        var oldFn = ctx.createScriptProcessor || ctx.createJavaScriptNode;
        var oldIsBest = $T("ZGlf::。由于{1}内部1秒375次回调，在移动端可能会有性能问题导致回调丢失录音变短，PC端无影响，暂不建议开启{1}。", 0, audioWorklet);
        var oldScript = function () {
            isWorklet = stream.isWorklet = false;
            _Disconn_n(stream);
            CLog($T("7TU0::Connect采用老的{1}，", 0, scriptProcessor)
                + i18n.get(Recorder[ConnectEnableWorklet] ?
                    $T("JwCL::但已设置{1}尝试启用{2}", 2)
                    : $T("VGjB::可设置{1}尝试启用{2}", 2)
                    , [RecTxt + "." + ConnectEnableWorklet + "=true", audioWorklet]
                ) + webMTips + oldIsBest, 3);

            var process = stream._p = oldFn.call(ctx, bufferSize, 1, 1);
            mediaConn(process);

            process.onaudioprocess = function (e) {
                var arr = e.inputBuffer.getChannelData(0);
                onReceive(arr);
            };
        };
        var connWorklet = function () {
            isWebM = stream.isWebM = false;
            _Disconn_r(stream);

            isWorklet = stream.isWorklet = !oldFn || Recorder[ConnectEnableWorklet];
            var AwNode = window.AudioWorkletNode;
            if (!(isWorklet && ctx[audioWorklet] && AwNode)) {
                oldScript();
                return;
            };
            var clazzUrl = function () {
                var xf = function (f) { return f.toString().replace(/^function|DEL_/g, "").replace(/\$RA/g, recAudioWorklet) };
                var clazz = 'class ' + RecProc + ' extends AudioWorkletProcessor{';
                clazz += "constructor " + xf(function (option) {
                    DEL_super(option);
                    var This = this, bufferSize = option.processorOptions.bufferSize;
                    This.bufferSize = bufferSize;
                    This.buffer = new Float32Array(bufferSize * 2);
                    This.pos = 0;
                    This.port.onmessage = function (e) {
                        if (e.data.kill) {
                            This.kill = true;
                            $C.log("$RA kill call");
                        }
                    };
                    $C.log("$RA .ctor call", option);
                });
                clazz += "process " + xf(function (input, b, c) {//需要等到ctx激活后才会有回调
                    var This = this, bufferSize = This.bufferSize;
                    var buffer = This.buffer, pos = This.pos;
                    input = (input[0] || [])[0] || [];
                    if (input.length) {
                        buffer.set(input, pos);
                        pos += input.length;

                        var len = ~~(pos / bufferSize) * bufferSize;
                        if (len) {
                            this.port.postMessage({ val: buffer.slice(0, len) });

                            var more = buffer.subarray(len, pos);
                            buffer = new Float32Array(bufferSize * 2);
                            buffer.set(more);
                            pos = more.length;
                            This.buffer = buffer;
                        }
                        This.pos = pos;
                    }
                    return !This.kill;
                });
                clazz += '}'
                    + 'try{'
                    + 'registerProcessor("' + RecProc + '", ' + RecProc + ')'
                    + '}catch(e){$C.error("' + recAudioWorklet + ' Reg Error",e)}';
                clazz = clazz.replace(/\$C\./g, "console.");
                return "data:text/javascript;base64," + btoa(unescape(encodeURIComponent(clazz)));
            };

            var awNext = function () {//可以继续，没有调用断开
                return isWorklet && stream._na;
            };
            var nodeAlive = stream._na = function () {
                if (badInt !== "") {//没有回调过数据
                    clearTimeout(badInt);
                    badInt = setTimeout(function () {
                        badInt = 0;
                        if (awNext()) {
                            CLog($T("MxX1::{1}未返回任何音频，恢复使用{2}", 0, audioWorklet, scriptProcessor), 3);
                            oldFn && oldScript();
                        };
                    }, 500);
                };
            };
            var createNode = function () {
                if (!awNext()) return;
                var node = stream._n = new AwNode(ctx, RecProc, {
                    processorOptions: { bufferSize: bufferSize }
                });
                mediaConn(node);
                node.port.onmessage = function (e) {
                    if (badInt) {
                        clearTimeout(badInt); badInt = "";
                    };
                    if (awNext()) {
                        onReceive(e.data.val);
                    } else if (!isWorklet) {
                        CLog($T("XUap::{1}多余回调", 0, audioWorklet), 3);
                    };
                };
                CLog($T("yOta::Connect采用{1}，设置{2}可恢复老式{3}", 0, audioWorklet, RecTxt + "." + ConnectEnableWorklet + "=false", scriptProcessor) + webMTips + oldIsBest, 3);
            };
            var ctxOK = function () {
                if (!awNext()) return;
                if (ctx[RecProc]) {
                    createNode();
                    return;
                };
                var url = clazzUrl();
                ctx[audioWorklet].addModule(url).then(function (e) {
                    if (!awNext()) return;
                    ctx[RecProc] = 1;
                    createNode();
                    if (badInt) {//重新计时
                        nodeAlive();
                    };
                })[CatchTxt](function (e) {
                    CLog(audioWorklet + ".addModule Error", 1, e);
                    awNext() && oldScript();
                });
            };
            ResumeCtx(ctx, function () { return awNext() }, ctxOK, ctxOK);
        };
        var connWebM = function () {
            var MR = window[MediaRecorderTxt];
            var onData = "ondataavailable";
            var webmType = "audio/webm; codecs=pcm";
            isWebM = stream.isWebM = Recorder[ConnectEnableWebM];

            var supportMR = MR && (onData in MR.prototype) && MR.isTypeSupported(webmType);
            webMTips = supportMR ? "" : $T("VwPd::（此浏览器不支持{1}）", 0, MRWebMPCM);
            if (!isUserMedia || !isWebM || !supportMR) {
                connWorklet();
                return;
            }

            var mrNext = function () {//可以继续，没有调用断开
                return isWebM && stream._ra;
            };
            var mrAlive = stream._ra = function () {
                if (badInt !== "") {//没有回调过数据
                    clearTimeout(badInt);
                    badInt = setTimeout(function () {
                        if (mrNext()) {
                            CLog($T("vHnb::{1}未返回任何音频，降级使用{2}", 0, MediaRecorderTxt, audioWorklet), 3);
                            connWorklet();
                        };
                    }, 500);
                };
            };

            var mrSet = Object.assign({ mimeType: webmType }, Recorder.ConnectWebMOptions);
            var mr = stream._r = new MR(stream, mrSet);
            var webmData = stream._rd = { sampleRate: ctx[sampleRateTxt] };
            mr[onData] = function (e) {
                var reader = new FileReader();
                reader.onloadend = function () {
                    if (mrNext()) {
                        var f32arr = WebM_Extract(new Uint8Array(reader.result), webmData);
                        if (!f32arr) return;
                        if (f32arr == -1) {//无法提取，立即降级
                            connWorklet();
                            return;
                        };

                        if (badInt) {
                            clearTimeout(badInt); badInt = "";
                        };
                        onReceive(f32arr);
                    } else if (!isWebM) {
                        CLog($T("O9P7::{1}多余回调", 0, MediaRecorderTxt), 3);
                    };
                };
                reader.readAsArrayBuffer(e.data);
            };
            mr.start(~~(bufferSize / 48));
            CLog($T("LMEm::Connect采用{1}，设置{2}可恢复使用{3}或老式{4}", 0, MRWebMPCM, RecTxt + "." + ConnectEnableWebM + "=false", audioWorklet, scriptProcessor));
        };

        connWebM();
    };
    var ConnAlive = function (stream) {
        if (stream._na) stream._na();
        if (stream._ra) stream._ra();
    };
    var _Disconn_n = function (stream) {
        stream._na = null;
        if (stream._n) {
            stream._n.port.postMessage({ kill: true });
            stream._n.disconnect();
            stream._n = null;
        };
    };
    var _Disconn_r = function (stream) {
        stream._ra = null;
        if (stream._r) {
            try { stream._r.stop() } catch (e) { CLog("mr stop err", 1, e) }
            stream._r = null;
        };
    };
    var Disconnect = function (streamStore) {
        streamStore = streamStore || Recorder;
        var isGlobal = streamStore == Recorder;

        var stream = streamStore.Stream;
        if (stream) {
            if (stream._m) {
                stream._m.disconnect();
                stream._m = null;
            };
            if (!stream._RC && stream._c) {//提供的runningContext不处理
                Recorder.CloseNewCtx(stream._c);
            };
            stream._RC = null; stream._c = null;
            if (stream._d) {
                StopS_(stream._d.stream);
                stream._d = null;
            };
            if (stream._p) {
                stream._p.disconnect();
                stream._p.onaudioprocess = stream._p = null;
            };
            _Disconn_n(stream);
            _Disconn_r(stream);

            if (isGlobal) {//全局的时候，要把流关掉（麦克风），直接提供的流不处理
                StopS_(stream);
            };
        };
        streamStore.Stream = 0;
    };
    var StopS_ = Recorder.StopS_ = function (stream) {
        var tracks = stream.getTracks && stream.getTracks() || stream.audioTracks || [];
        for (var i = 0; i < tracks.length; i++) {
            var track = tracks[i];
            track.stop && track.stop();
        };
        stream.stop && stream.stop();
    };


    Recorder.SampleData = function (pcmDatas, pcmSampleRate, newSampleRate, prevChunkInfo, option) {
        var Txt = "SampleData";
        prevChunkInfo || (prevChunkInfo = {});
        var index = prevChunkInfo.index || 0;
        var offset = prevChunkInfo.offset || 0;

        var filter = prevChunkInfo.filter;
        if (filter && filter.fn && filter.sr != pcmSampleRate) {
            filter = null; CLog($T("d48C::{1}的filter采样率变了，重设滤波", 0, Txt), 3);
        };
        if (!filter) {//采样率差距比较大才开启低通滤波，最高频率用新采样率频率的3/4
            var freq = newSampleRate > pcmSampleRate * 3 / 4 ? 0 : newSampleRate / 2 * 3 / 4;
            filter = { fn: freq ? Recorder.IIRFilter(true, pcmSampleRate, freq) : 0 };
        };
        filter.sr = pcmSampleRate;
        var filterFn = filter.fn;

        var frameNext = prevChunkInfo.frameNext || [];
        option || (option = {});
        var frameSize = option.frameSize || 1;
        if (option.frameType) {
            frameSize = option.frameType == "mp3" ? 1152 : 1;
        };

        var nLen = pcmDatas.length;
        if (index > nLen + 1) {
            CLog($T("tlbC::{1}似乎传入了未重置chunk {2}", 0, Txt, index + ">" + nLen), 3);
        };
        var size = 0;
        for (var i = index; i < nLen; i++) {
            size += pcmDatas[i].length;
        };
        size = Math.max(0, size - Math.floor(offset));
        var step = pcmSampleRate / newSampleRate;
        if (step > 1) {//新采样低于录音采样，进行抽样
            size = Math.floor(size / step);
        } else {//新采样高于录音采样不处理，省去了插值处理
            step = 1;
            newSampleRate = pcmSampleRate;
        };

        size += frameNext.length;
        var res = new Int16Array(size);
        var idx = 0;
        for (var i = 0; i < frameNext.length; i++) {
            res[idx] = frameNext[i];
            idx++;
        };
        for (; index < nLen; index++) {
            var o = pcmDatas[index];
            var i = offset, il = o.length;
            var F = filterFn && filterFn.Embed, F1 = 0, F2 = 0, Fx = 0, Fy = 0;
            for (var i0 = 0, i2 = 0; i0 < il; i0++, i2++) {
                if (i2 < il) {
                    if (F) {//IIRFilter代码内置，比函数调用快4倍
                        Fx = o[i2];
                        Fy = F.b0 * Fx + F.b1 * F.x1 + F.b0 * F.x2 - F.a1 * F.y1 - F.a2 * F.y2;
                        F.x2 = F.x1; F.x1 = Fx; F.y2 = F.y1; F.y1 = Fy;
                    } else { Fy = filterFn ? filterFn(o[i2]) : o[i2]; }
                }
                F1 = F2; F2 = Fy;
                if (i2 == 0) { i0--; continue; }
                var before = Math.floor(i);
                if (i0 != before) continue;
                var after = Math.ceil(i);
                var atPoint = i - before;

                var beforeVal = F1;
                var afterVal = after < il ? F2 : beforeVal;
                var val = beforeVal + (afterVal - beforeVal) * atPoint;

                if (val > 0x7FFF) val = 0x7FFF; else if (val < -0x8000) val = -0x8000;
                res[idx] = val;

                idx++;
                i += step;
            };
            offset = Math.max(0, i - il);
        };
        frameNext = null;
        var frameNextSize = res.length % frameSize;
        if (frameNextSize > 0) {
            var u8Pos = (res.length - frameNextSize) * 2;
            frameNext = new Int16Array(res.buffer.slice(u8Pos));
            res = new Int16Array(res.buffer.slice(0, u8Pos));
        };

        return {
            index: index
            , offset: offset
            , filter: filter

            , frameNext: frameNext
            , sampleRate: newSampleRate
            , data: res
        };
    };


    Recorder.IIRFilter = function (useLowPass, sampleRate, freq) {
        var ov = 2 * Math.PI * freq / sampleRate;
        var sn = Math.sin(ov);
        var cs = Math.cos(ov);
        var alpha = sn / 2;

        var a0 = 1 + alpha;
        var a1 = (-2 * cs) / a0;
        var a2 = (1 - alpha) / a0;
        if (useLowPass) {
            var b0 = (1 - cs) / 2 / a0;
            var b1 = (1 - cs) / a0;
        } else {
            var b0 = (1 + cs) / 2 / a0;
            var b1 = -(1 + cs) / a0;
        }

        var x1 = 0, x2 = 0, y = 0, y1 = 0, y2 = 0;
        var fn = function (x) {
            y = b0 * x + b1 * x1 + b0 * x2 - a1 * y1 - a2 * y2;
            x2 = x1; x1 = x;
            y2 = y1; y1 = y;
            return y;
        };
        fn.Embed = { x1: 0, x2: 0, y1: 0, y2: 0, b0: b0, b1: b1, a1: a1, a2: a2 };
        return fn;
    };



    Recorder.PowerLevel = function (pcmAbsSum, pcmLength) {

        var power = (pcmAbsSum / pcmLength) || 0;//NaN
        var level;
        if (power < 1251) {//1250的结果10%，更小的音量采用线性取值
            level = Math.round(power / 1250 * 10);
        } else {
            level = Math.round(Math.min(100, Math.max(0, (1 + Math.log(power / 10000) / Math.log(10)) * 100)));
        };
        return level;
    };


    Recorder.PowerDBFS = function (maxSample) {
        var val = Math.max(0.1, maxSample || 0), Pref = 0x7FFF;
        val = Math.min(val, Pref);
        val = 20 * Math.log(val / Pref) / Math.log(10);
        return Math.max(-100, Math.round(val));
    };
    Recorder.CLog = function (msg, err) {
        if (typeof console != "object") return;
        var now = new Date();
        var t = ("0" + now.getMinutes()).substr(-2)
            + ":" + ("0" + now.getSeconds()).substr(-2)
            + "." + ("00" + now.getMilliseconds()).substr(-3);
        var recID = this && this.envIn && this.envCheck && this.id;
        var arr = ["[" + t + " " + RecTxt + (recID ? ":" + recID : "") + "]" + msg];
        var a = arguments, cwe = Recorder.CLog;
        var i = 2, fn = cwe.log || console.log;
        if (IsNum(err)) {
            fn = err == 1 ? cwe.error || console.error : err == 3 ? cwe.warn || console.warn : fn;
        } else {
            i = 1;
        };
        for (; i < a.length; i++) {
            arr.push(a[i]);
        };
        if (IsLoser) {
            fn && fn("[IsLoser]" + arr[0], arr.length > 1 ? arr : "");
        } else {
            // fn.apply(console,arr);
        };
    };
    var CLog = function () { Recorder.CLog.apply(this, arguments); };
    var IsLoser = true; try { IsLoser = !console.log.apply; } catch (e) { };




    var ID = 0;
    function initFn(set) {
        var This = this; This.id = ++ID;
        Traffic();


        var o = {
            type: "mp3"

            , onProcess: NOOP
        };

        for (var k in set) {
            o[k] = set[k];
        };
        This.set = o;

        var vB = o[bitRateTxt], vS = o[sampleRateTxt];
        if (vB && !IsNum(vB) || vS && !IsNum(vS)) {
            This.CLog($T.G("IllegalArgs-1", [$T("VtS4::{1}和{2}必须是数值", 0, sampleRateTxt, bitRateTxt)]), 1, set);
        };
        o[bitRateTxt] = +vB || 16;
        o[sampleRateTxt] = +vS || 16000;

        This.state = 0;
        This._S = 9;
        This.Sync = { O: 9, C: 9 };
    };
    Recorder.Sync = { O: 9, C: 9 };

    Recorder.prototype = initFn.prototype = {
        CLog: CLog
        , _streamStore: function () {
            if (this.set.sourceStream) {
                return this;
            } else {
                return Recorder;
            }
        }
        , _streamCtx: function () {
            var m = this._streamStore().Stream;
            return m && m._c;
        }
        , open: function (True, False) {
            var This = this, set = This.set, streamStore = This._streamStore(), newCtx = 0;
            True = True || NOOP;
            var failCall = function (errMsg, isUserNotAllow) {
                isUserNotAllow = !!isUserNotAllow;
                This.CLog($T("5tWi::录音open失败：") + errMsg + ",isUserNotAllow:" + isUserNotAllow, 1);
                if (newCtx) Recorder.CloseNewCtx(newCtx);
                False && False(errMsg, isUserNotAllow);
            };

            This._streamTag = getUserMediaTxt;
            var ok = function () {
                This.CLog("open ok, id:" + This.id + " stream:" + This._streamTag);
                True();

                This._SO = 0;
            };
            var Lock = streamStore.Sync;
            var lockOpen = ++Lock.O, lockClose = Lock.C;
            This._O = This._O_ = lockOpen;
            This._SO = This._S;
            var lockFail = function () {
                if (lockClose != Lock.C || !This._O) {
                    var err = $T("dFm8::open被取消");
                    if (lockOpen == Lock.O) {
                        This.close();
                    } else {
                        err = $T("VtJO::open被中断");
                    };
                    failCall(err);
                    return true;
                };
            };
            if (!isBrowser) {
                failCall($T.G("NonBrowser-1", ["open"]) + $T("EMJq::，可尝试使用RecordApp解决方案") + "(" + GitUrl + "/tree/master/app-support-sample)");
                return;
            };
            var checkMsg = This.envCheck({ envName: "H5", canProcess: true });
            if (checkMsg) {
                failCall($T("A5bm::不能录音：") + checkMsg);
                return;
            };
            if (set.sourceStream) {
                This._streamTag = "set.sourceStream";
                if (!Recorder.GetContext()) {
                    failCall($T("1iU7::不支持此浏览器从流中获取录音"));
                    return;
                };

                Disconnect(streamStore);
                var stream = This.Stream = set.sourceStream;
                stream._RC = set.runningContext;
                stream._call = {};

                try {
                    Connect(streamStore);
                } catch (e) {
                    Disconnect(streamStore);
                    failCall($T("BTW2::从流中打开录音失败：") + e.message);
                    return;
                }
                ok();
                return;
            };
            var codeFail = function (code, msg) {
                try {
                    window.top.a;
                } catch (e) {
                    failCall($T("Nclz::无权录音(跨域，请尝试给iframe添加麦克风访问策略，如{1})", 0, 'allow="camera;microphone"'));
                    return;
                };

                if (/Permission|Allow/i.test(code)) {
                    failCall($T("gyO5::用户拒绝了录音权限"), true);
                } else if (window.isSecureContext === false) {
                    failCall($T("oWNo::浏览器禁止不安全页面录音，可开启https解决"));
                } else if (/Found/i.test(code)) {
                    failCall(msg + $T("jBa9::，无可用麦克风"));
                } else {
                    failCall(msg);
                };
            };
            if (Recorder.IsOpen()) {
                ok();
                return;
            };
            if (!Recorder.Support()) {
                codeFail("", $T("COxc::此浏览器不支持录音"));
                return;
            };
            var ctx = set.runningContext;
            if (!ctx) ctx = newCtx = Recorder.GetContext(true);
            var f1 = function (stream) {
                setTimeout(function () {
                    stream._call = {};
                    var oldStream = Recorder.Stream;
                    if (oldStream) {
                        Disconnect();
                        stream._call = oldStream._call;
                    };
                    Recorder.Stream = stream;
                    stream._c = ctx;
                    stream._RC = set.runningContext;
                    if (lockFail()) return;

                    if (Recorder.IsOpen()) {
                        if (oldStream) This.CLog($T("upb8::发现同时多次调用open"), 1);

                        Connect(streamStore, 1);
                        ok();
                    } else {
                        failCall($T("Q1GA::录音功能无效：无音频流"));
                    };
                }, 100);
            };
            var f2 = function (e) {
                var code = e.name || e.message || e.code + ":" + e;
                This.CLog($T("xEQR::请求录音权限错误"), 1, e);

                codeFail(code, $T("bDOG::无法录音：") + code);
            };

            var trackSet = set.audioTrackSet || {};
            trackSet[sampleRateTxt] = ctx[sampleRateTxt];

            var mSet = { audio: trackSet };
            try {
                var pro = Recorder.Scope[getUserMediaTxt](mSet, f1, f2);
            } catch (e) {
                This.CLog(getUserMediaTxt, 3, e);
                mSet = { audio: true };
                pro = Recorder.Scope[getUserMediaTxt](mSet, f1, f2);
            };
            This.CLog(getUserMediaTxt + "(" + JSON.stringify(mSet) + ") " + CtxState(ctx)
                + $T("RiWe::，未配置noiseSuppression和echoCancellation时浏览器可能会自动打开降噪和回声消除，移动端可能会降低系统播放音量（关闭录音后可恢复），请参阅文档中audioTrackSet配置")
                + "(" + GitUrl + ") LM:" + LM + " UA:" + navigator.userAgent);
            if (pro && pro.then) {
                pro.then(f1)[CatchTxt](f2);
            };
        }
        , close: function (call) {
            call = call || NOOP;

            var This = this, streamStore = This._streamStore();
            This._stop();
            var sTag = " stream:" + This._streamTag;

            var Lock = streamStore.Sync;
            This._O = 0;
            if (This._O_ != Lock.O) {
                This.CLog($T("hWVz::close被忽略（因为同时open了多个rec，只有最后一个会真正close）") + sTag, 3);
                call();
                return;
            };
            Lock.C++;

            Disconnect(streamStore);

            This.CLog("close," + sTag);
            call();
        }






        , mock: function (pcmData, pcmSampleRate) {
            var This = this;
            This._stop();

            This.isMock = 1;
            This.mockEnvInfo = null;
            This.buffers = [pcmData];
            This.recSize = pcmData.length;
            This._setSrcSR(pcmSampleRate);
            This._streamTag = "mock";
            return This;
        }
        , _setSrcSR: function (sampleRate) {
            var This = this, set = This.set;
            var setSr = set[sampleRateTxt];
            if (setSr > sampleRate) {
                set[sampleRateTxt] = sampleRate;
            } else { setSr = 0 }
            This[srcSampleRateTxt] = sampleRate;
            This.CLog(srcSampleRateTxt + ": " + sampleRate + " set." + sampleRateTxt + ": " + set[sampleRateTxt] + (setSr ? " " + $T("UHvm::忽略") + ": " + setSr : ""), setSr ? 3 : 0);
        }
        , envCheck: function (envInfo) {
            var errMsg, This = this, set = This.set;
            var tag = "CPU_BE";
            if (!errMsg && !Recorder[tag] && typeof Int8Array == "function" && !new Int8Array(new Int32Array([1]).buffer)[0]) {
                Traffic(tag);
                errMsg = $T("Essp::不支持{1}架构", 0, tag);
            };
            if (!errMsg) {
                var type = set.type, hasFn = This[type + "_envCheck"];
                if (set.takeoffEncodeChunk) {
                    if (!hasFn) {
                        errMsg = $T("2XBl::{1}类型不支持设置takeoffEncodeChunk", 0, type) + (This[type] ? "" : $T("LG7e::(未加载编码器)"));
                    } else if (!envInfo.canProcess) {
                        errMsg = $T("7uMV::{1}环境不支持实时处理", 0, envInfo.envName);
                    };
                };

                if (!errMsg && hasFn) {
                    errMsg = This[type + "_envCheck"](envInfo, set);
                };
            };

            return errMsg || "";
        }
        , envStart: function (mockEnvInfo, sampleRate) {
            var This = this, set = This.set;
            This.isMock = mockEnvInfo ? 1 : 0;
            This.mockEnvInfo = mockEnvInfo;
            This.buffers = [];
            This.recSize = 0;
            if (mockEnvInfo) {
                This._streamTag = "env$" + mockEnvInfo.envName;
            };

            This.state = 1;//运行状态，0未录音 1录音中 2暂停 3等待ctx激活
            This.envInLast = 0;//envIn接收到最后录音内容的时间
            This.envInFirst = 0;//envIn接收到的首个录音内容的录制时间
            This.envInFix = 0;//补偿的总时间
            This.envInFixTs = [];//补偿计数列表
            This._setSrcSR(sampleRate);

            This.engineCtx = 0;
            if (This[set.type + "_start"]) {
                var engineCtx = This.engineCtx = This[set.type + "_start"](set);
                if (engineCtx) {
                    engineCtx.pcmDatas = [];
                    engineCtx.pcmSize = 0;
                };
            };
        }
        , envResume: function () {//和平台环境无关的恢复录音
            this.envInFixTs = [];
        }
        , envIn: function (pcm, sum) {//和平台环境无关的pcm[Int16]输入
            var This = this, set = This.set, engineCtx = This.engineCtx;
            if (This.state != 1) {
                if (!This.state) This.CLog("envIn at state=0", 3);
                return;
            };
            var bufferSampleRate = This[srcSampleRateTxt];
            var size = pcm.length;
            var powerLevel = Recorder.PowerLevel(sum, size);

            var buffers = This.buffers;
            var bufferFirstIdx = buffers.length;//之前的buffer都是经过onProcess处理好的，不允许再修改
            buffers.push(pcm);
            var buffersThis = buffers;
            var bufferFirstIdxThis = bufferFirstIdx;
            var now = Date.now();
            var pcmTime = Math.round(size / bufferSampleRate * 1000);
            This.envInLast = now;
            if (This.buffers.length == 1) {//记下首个录音数据的录制时间
                This.envInFirst = now - pcmTime;
            };
            var envInFixTs = This.envInFixTs;
            envInFixTs.splice(0, 0, { t: now, d: pcmTime });
            var tsInStart = now, tsPcm = 0;
            for (var i = 0; i < envInFixTs.length; i++) {
                var o = envInFixTs[i];
                if (now - o.t > 3000) {
                    envInFixTs.length = i;
                    break;
                };
                tsInStart = o.t;
                tsPcm += o.d;
            };
            var tsInPrev = envInFixTs[1];
            var tsIn = now - tsInStart;
            var lost = tsIn - tsPcm;
            if (lost > tsIn / 3 && (tsInPrev && tsIn > 1000 || envInFixTs.length >= 6)) {
                var addTime = now - tsInPrev.t - pcmTime;//距离上次输入丢失这么多ms
                if (addTime > pcmTime / 5) {//丢失超过本帧的1/5
                    var fixOpen = !set.disableEnvInFix;
                    This.CLog("[" + now + "]" + i18n.get(fixOpen ? $T("4Kfd::补偿{1}ms", 1) : $T("bM5i::未补偿{1}ms", 1), [addTime]), 3);
                    This.envInFix += addTime;
                    if (fixOpen) {
                        var addPcm = new Int16Array(addTime * bufferSampleRate / 1000);
                        size += addPcm.length;
                        buffers.push(addPcm);
                    };
                };
            };


            var sizeOld = This.recSize, addSize = size;
            var bufferSize = sizeOld + addSize;
            This.recSize = bufferSize;//此值在onProcess后需要修正，可能新数据被修改
            if (engineCtx) {
                var chunkInfo = Recorder.SampleData(buffers, bufferSampleRate, set[sampleRateTxt], engineCtx.chunkInfo);
                engineCtx.chunkInfo = chunkInfo;

                sizeOld = engineCtx.pcmSize;
                addSize = chunkInfo.data.length;
                bufferSize = sizeOld + addSize;
                engineCtx.pcmSize = bufferSize;//此值在onProcess后需要修正，可能新数据被修改

                buffers = engineCtx.pcmDatas;
                bufferFirstIdx = buffers.length;
                buffers.push(chunkInfo.data);
                bufferSampleRate = chunkInfo[sampleRateTxt];
            };

            var duration = Math.round(bufferSize / bufferSampleRate * 1000);
            var bufferNextIdx = buffers.length;
            var bufferNextIdxThis = buffersThis.length;
            var asyncEnd = function () {
                var num = asyncBegin ? 0 : -addSize;
                var hasClear = buffers[0] == null;
                for (var i = bufferFirstIdx; i < bufferNextIdx; i++) {
                    var buffer = buffers[i];
                    if (buffer == null) {//已被主动释放内存，比如长时间实时传输录音时
                        hasClear = 1;
                    } else {
                        num += buffer.length;
                        if (engineCtx && buffer.length) {
                            This[set.type + "_encode"](engineCtx, buffer);
                        };
                    };
                };
                if (hasClear && engineCtx) {
                    var i = bufferFirstIdxThis;
                    if (buffersThis[0]) {
                        i = 0;
                    };
                    for (; i < bufferNextIdxThis; i++) {
                        buffersThis[i] = null;
                    };
                };
                if (hasClear) {
                    num = asyncBegin ? addSize : 0;

                    buffers[0] = null;//彻底被清理
                };
                if (engineCtx) {
                    engineCtx.pcmSize += num;
                } else {
                    This.recSize += num;
                };
            };
            var asyncBegin = 0, procTxt = "rec.set.onProcess";
            try {
                asyncBegin = set.onProcess(buffers, powerLevel, duration, bufferSampleRate, bufferFirstIdx, asyncEnd);
            } catch (e) {
                console.error(procTxt + $T("gFUF::回调出错是不允许的，需保证不会抛异常"), e);
            };

            var slowT = Date.now() - now;
            if (slowT > 10 && This.envInFirst - now > 1000) {
                This.CLog(procTxt + $T("2ghS::低性能，耗时{1}ms", 0, slowT), 3);
            };

            if (asyncBegin === true) {
                var hasClear = 0;
                for (var i = bufferFirstIdx; i < bufferNextIdx; i++) {
                    if (buffers[i] == null) {//已被主动释放内存，比如长时间实时传输录音时 ，但又要开启异步模式，此种情况是非法的
                        hasClear = 1;
                    } else {
                        buffers[i] = new Int16Array(0);
                    };
                };

                if (hasClear) {
                    This.CLog($T("ufqH::未进入异步前不能清除buffers"), 3);
                } else {
                    if (engineCtx) {
                        engineCtx.pcmSize -= addSize;
                    } else {
                        This.recSize -= addSize;
                    };
                };
            } else {
                asyncEnd();
            };
        }
        , start: function () {
            var This = this;

            var isOpen = 1;
            if (This.set.sourceStream) {//直接提供了流，仅判断是否调用了open
                if (!This.Stream) {
                    isOpen = 0;
                }
            } else if (!Recorder.IsOpen()) {//监测全局麦克风是否打开并且有效
                isOpen = 0;
            };
            if (!isOpen) {
                This.CLog($T("6WmN::start失败：未open"), 1);
                return;
            };
            var ctx = This._streamCtx();
            This.CLog($T("kLDN::start 开始录音，") + CtxState(ctx) + " stream:" + This._streamTag);

            This._stop();
            This.envStart(null, ctx[sampleRateTxt]);
            This.state = 3;//0未录音 1录音中 2暂停 3等待ctx激活
            if (This._SO && This._SO + 1 != This._S) {//上面调用过一次 _stop
                This.CLog($T("Bp2y::start被中断"), 3);
                return;
            };
            This._SO = 0;

            var end = function () {
                if (This.state == 3) {
                    This.state = 1;
                    This.resume();
                }
            };
            var tag = "AudioContext resume: ";
            ResumeCtx(ctx, function (runC) {
                runC && This.CLog(tag + "wait...");
                return This.state == 3;
            }, function (runC) {
                runC && This.CLog(tag + ctx.state);
                end();
            }, function (err) {
                This.CLog(tag + ctx.state + $T("upkE::，可能无法录音：") + err, 1);
                end();
            });
        }




        , pause: function () {
            var This = this, stream = This._streamStore().Stream;
            if (This.state) {
                This.state = 2;
                This.CLog("pause");
                if (stream) delete stream._call[This.id];
            };
        }

        , resume: function () {
            var This = this, stream = This._streamStore().Stream;
            var tag = "resume", tag3 = tag + "(wait ctx)";
            if (This.state == 3) {
                This.CLog(tag3);
            } else if (This.state) {
                This.state = 1;
                This.CLog(tag);
                This.envResume();

                if (stream) {
                    stream._call[This.id] = function (pcm, sum) {
                        if (This.state == 1) {
                            This.envIn(pcm, sum);
                        };
                    };
                    ConnAlive(stream);//AudioWorklet只会在ctx激活后运行
                };

                var ctx = This._streamCtx();
                if (ctx) {
                    ResumeCtx(ctx, function (runC) {
                        runC && This.CLog(tag3 + "...");
                        return This.state == 1;
                    }, function (runC) {
                        runC && This.CLog(tag3 + ctx.state);
                        ConnAlive(stream);
                    }, function (err) {
                        This.CLog(tag3 + ctx.state + "[err]" + err, 1);
                    });
                };
            };
        }




        , _stop: function (keepEngine) {
            var This = this, set = This.set;
            if (!This.isMock) {
                This._S++;
            };
            if (This.state) {
                This.pause();
                This.state = 0;
            };
            if (!keepEngine && This[set.type + "_stop"]) {
                This[set.type + "_stop"](This.engineCtx);
                This.engineCtx = 0;
            };
        }

        , stop: function (True, False, autoClose) {
            var This = this, set = This.set, t1;
            var envInMS = This.envInLast - This.envInFirst, envInLen = envInMS && This.buffers.length;
            This.CLog($T("Xq4s::stop 和start时差:")
                + (envInMS ? envInMS + "ms " + $T("3CQP::补偿:") + This.envInFix + "ms"
                    + " envIn:" + envInLen + " fps:" + (envInLen / envInMS * 1000).toFixed(1)
                    : "-") + " stream:" + This._streamTag + " (" + GitUrl + ") LM:" + LM);

            var end = function () {
                This._stop();//彻底关掉engineCtx
                if (autoClose) {
                    This.close();
                };
            };
            var err = function (msg) {
                This.CLog($T("u8JG::结束录音失败：") + msg, 1);
                False && False(msg);
                end();
            };
            var ok = function (blob, mime, duration) {
                var tBlob = "blob", tABuf = "arraybuffer", tDT = "dataType", tDDT = "DefaultDataType";
                var dType = This[tDT] || Recorder[tDDT] || tBlob, dTag = tDT + "=" + dType;
                var isAB = blob instanceof ArrayBuffer, dErr = 0;
                var dLen = isAB ? blob.byteLength : blob.size;
                if (dType == tABuf) {
                    if (!isAB) dErr = 1;
                } else if (dType == tBlob) {
                    if (typeof Blob != "function") {
                        dErr = $T.G("NonBrowser-1", [dTag]) + $T("1skY::，请设置{1}", 0, RecTxt + "." + tDDT + '="' + tABuf + '"');
                    } else {
                        if (isAB) blob = new Blob([blob], { type: mime });
                        if (!(blob instanceof Blob)) dErr = 1;
                        mime = blob.type || mime;
                    }
                } else {
                    dErr = $T.G("NotSupport-1", [dTag]);
                };

                This.CLog($T("Wv7l::结束录音 编码花{1}ms 音频时长{2}ms 文件大小{3}b", 0, Date.now() - t1, duration, dLen) + " " + dTag + "," + mime);
                if (dErr) {
                    err(dErr != 1 ? dErr : $T("Vkbd::{1}编码器返回的不是{2}", 0, set.type, dType) + ", " + dTag);
                    return;
                };
                if (set.takeoffEncodeChunk) {//接管了输出，此时blob长度为0
                    This.CLog($T("QWnr::启用takeoffEncodeChunk后stop返回的blob长度为0不提供音频数据"), 3);
                } else if (dLen < Math.max(50, duration / 5)) {//1秒小于0.2k？
                    err($T("Sz2H::生成的{1}无效", 0, set.type));
                    return;
                };

                True && True(blob, duration, mime);
                end();
            };
            if (!This.isMock) {
                var isCtxWait = This.state == 3;
                if (!This.state || isCtxWait) {
                    err($T("wf9t::未开始录音") + (isCtxWait ? $T("Dl2c::，开始录音前无用户交互导致AudioContext未运行") : ""));
                    return;
                };
            };
            This._stop(true);
            var size = This.recSize;
            if (!size) {
                err($T("Ltz3::未采集到录音"));
                return;
            };
            if (!This[set.type]) {
                err($T("xGuI::未加载{1}编码器，请尝试到{2}的src/engine内找到{1}的编码器并加载", 0, set.type, RecTxt));
                return;
            };
            if (This.isMock) {
                var checkMsg = This.envCheck(This.mockEnvInfo || { envName: "mock", canProcess: false });//没有提供环境信息的mock时没有onProcess回调
                if (checkMsg) {
                    err($T("AxOH::录音错误：") + checkMsg);
                    return;
                };
            };
            var engineCtx = This.engineCtx;
            if (This[set.type + "_complete"] && engineCtx) {
                var duration = Math.round(engineCtx.pcmSize / set[sampleRateTxt] * 1000);//采用后的数据长度和buffers的长度可能微小的不一致，是采样率连续转换的精度问题

                t1 = Date.now();
                This[set.type + "_complete"](engineCtx, function (blob, mime) {
                    ok(blob, mime, duration);
                }, err);
                return;
            };
            t1 = Date.now();
            if (!This.buffers[0]) {
                err($T("xkKd::音频buffers被释放"));
                return;
            };
            var chunk = Recorder.SampleData(This.buffers, This[srcSampleRateTxt], set[sampleRateTxt]);

            set[sampleRateTxt] = chunk[sampleRateTxt];
            var res = chunk.data;
            var duration = Math.round(res.length / set[sampleRateTxt] * 1000);

            This.CLog($T("CxeT::采样:{1} 花:{2}ms", 0, size + "->" + res.length, Date.now() - t1));

            setTimeout(function () {
                t1 = Date.now();
                This[set.type](res, function (blob, mime) {
                    ok(blob, mime, duration);
                }, function (msg) {
                    err(msg);
                });
            });
        }

    };
    var WebM_Extract = function (inBytes, scope) {
        if (!scope.pos) {
            scope.pos = [0]; scope.tracks = {}; scope.bytes = [];
        };
        var tracks = scope.tracks, position = [scope.pos[0]];
        var endPos = function () { scope.pos[0] = position[0] };

        var sBL = scope.bytes.length;
        var bytes = new Uint8Array(sBL + inBytes.length);
        bytes.set(scope.bytes); bytes.set(inBytes, sBL);
        scope.bytes = bytes;
        if (!scope._ht) {
            readMatroskaVInt(bytes, position);//EBML Header
            readMatroskaBlock(bytes, position);//跳过EBML Header内容
            if (!BytesEq(readMatroskaVInt(bytes, position), [0x18, 0x53, 0x80, 0x67])) {
                return;//未识别到Segment
            }
            readMatroskaVInt(bytes, position);//跳过Segment长度值
            while (position[0] < bytes.length) {
                var eid0 = readMatroskaVInt(bytes, position);
                var bytes0 = readMatroskaBlock(bytes, position);
                var pos0 = [0], audioIdx = 0;
                if (!bytes0) return;//数据不全，等待缓冲
                if (BytesEq(eid0, [0x16, 0x54, 0xAE, 0x6B])) {
                    while (pos0[0] < bytes0.length) {
                        var eid1 = readMatroskaVInt(bytes0, pos0);
                        var bytes1 = readMatroskaBlock(bytes0, pos0);
                        var pos1 = [0], track = { channels: 0, sampleRate: 0 };
                        if (BytesEq(eid1, [0xAE])) {//TrackEntry
                            while (pos1[0] < bytes1.length) {
                                var eid2 = readMatroskaVInt(bytes1, pos1);
                                var bytes2 = readMatroskaBlock(bytes1, pos1);
                                var pos2 = [0];
                                if (BytesEq(eid2, [0xD7])) {//Track Number
                                    var val = BytesInt(bytes2);
                                    track.number = val;
                                    tracks[val] = track;
                                } else if (BytesEq(eid2, [0x83])) {//Track Type
                                    var val = BytesInt(bytes2);
                                    if (val == 1) track.type = "video";
                                    else if (val == 2) {
                                        track.type = "audio";
                                        if (!audioIdx) scope.track0 = track;
                                        track.idx = audioIdx++;
                                    } else track.type = "Type-" + val;
                                } else if (BytesEq(eid2, [0x86])) {//Track Codec
                                    var str = "";
                                    for (var i = 0; i < bytes2.length; i++) {
                                        str += String.fromCharCode(bytes2[i]);
                                    }
                                    track.codec = str;
                                } else if (BytesEq(eid2, [0xE1])) {
                                    while (pos2[0] < bytes2.length) {//循环读取 Audio 属性
                                        var eid3 = readMatroskaVInt(bytes2, pos2);
                                        var bytes3 = readMatroskaBlock(bytes2, pos2);
                                        if (BytesEq(eid3, [0xB5])) {
                                            var val = 0, arr = new Uint8Array(bytes3.reverse()).buffer;
                                            if (bytes3.length == 4) val = new Float32Array(arr)[0];
                                            else if (bytes3.length == 8) val = new Float64Array(arr)[0];
                                            else CLog("WebM Track !Float", 1, bytes3);
                                            track[sampleRateTxt] = Math.round(val);
                                        } else if (BytesEq(eid3, [0x62, 0x64])) track.bitDepth = BytesInt(bytes3);
                                        else if (BytesEq(eid3, [0x9F])) track.channels = BytesInt(bytes3);
                                    }
                                }
                            }
                        }
                    };
                    scope._ht = 1;
                    CLog("WebM Tracks", tracks);
                    endPos();
                    break;
                }
            }
        }
        var track0 = scope.track0;
        if (!track0) return;
        if (track0.bitDepth == 16 && /FLOAT/i.test(track0.codec)) {
            track0.bitDepth = 32;
            CLog("WebM 16->32 bit", 3);
        }
        if (track0[sampleRateTxt] != scope[sampleRateTxt] || track0.bitDepth != 32 || track0.channels < 1 || !/(\b|_)PCM\b/i.test(track0.codec)) {
            scope.bytes = [];//格式非预期 无法处理，清空缓冲数据
            if (!scope.bad) CLog("WebM Track Unexpected", 3, scope);
            scope.bad = 1;
            return -1;
        }
        var datas = [], dataLen = 0;
        while (position[0] < bytes.length) {
            var eid1 = readMatroskaVInt(bytes, position);
            var bytes1 = readMatroskaBlock(bytes, position);
            if (!bytes1) break;//数据不全，等待缓冲
            if (BytesEq(eid1, [0xA3])) {//SimpleBlock完整数据
                var trackNo = bytes1[0] & 0xf;
                var track = tracks[trackNo];
                if (!track) {//不可能没有，数据出错？
                    CLog("WebM !Track" + trackNo, 1, tracks);
                } else if (track.idx === 0) {
                    var u8arr = new Uint8Array(bytes1.length - 4);
                    for (var i = 4; i < bytes1.length; i++) {
                        u8arr[i - 4] = bytes1[i];
                    }
                    datas.push(u8arr); dataLen += u8arr.length;
                }
            }
            endPos();
        }

        if (dataLen) {
            var more = new Uint8Array(bytes.length - scope.pos[0]);
            more.set(bytes.subarray(scope.pos[0]));
            scope.bytes = more;
            scope.pos[0] = 0;

            var u8arr = new Uint8Array(dataLen);
            for (var i = 0, i2 = 0; i < datas.length; i++) {
                u8arr.set(datas[i], i2);
                i2 += datas[i].length;
            }
            var arr = new Float32Array(u8arr.buffer);

            if (track0.channels > 1) {//多声道，提取一个声道
                var arr2 = [];
                for (var i = 0; i < arr.length;) {
                    arr2.push(arr[i]);
                    i += track0.channels;
                }
                arr = new Float32Array(arr2);
            };
            return arr;
        }
    };
    var BytesEq = function (bytes1, bytes2) {
        if (!bytes1 || bytes1.length != bytes2.length) return false;
        if (bytes1.length == 1) return bytes1[0] == bytes2[0];
        for (var i = 0; i < bytes1.length; i++) {
            if (bytes1[i] != bytes2[i]) return false;
        }
        return true;
    };
    var BytesInt = function (bytes) {
        var s = "";//0-8字节，js位运算只支持4字节
        for (var i = 0; i < bytes.length; i++) { var n = bytes[i]; s += (n < 16 ? "0" : "") + n.toString(16) };
        return parseInt(s, 16) || 0;
    };
    var readMatroskaVInt = function (arr, pos, trim) {
        var i = pos[0];
        if (i >= arr.length) return;
        var b0 = arr[i], b2 = ("0000000" + b0.toString(2)).substr(-8);
        var m = /^(0*1)(\d*)$/.exec(b2);
        if (!m) return;
        var len = m[1].length, val = [];
        if (i + len > arr.length) return;
        for (var i2 = 0; i2 < len; i2++) { val[i2] = arr[i]; i++; }
        if (trim) val[0] = parseInt(m[2] || '0', 2);
        pos[0] = i;
        return val;
    };
    var readMatroskaBlock = function (arr, pos) {
        var lenVal = readMatroskaVInt(arr, pos, 1);
        if (!lenVal) return;
        var len = BytesInt(lenVal);
        var i = pos[0], val = [];
        if (len < 0x7FFFFFFF) {
            if (i + len > arr.length) return;
            for (var i2 = 0; i2 < len; i2++) { val[i2] = arr[i]; i++; }
        }
        pos[0] = i;
        return val;
    };
    var i18n = Recorder.i18n = {
        lang: "zh-CN"
        , alias: { "zh-CN": "zh", "en-US": "en" }
        , locales: {}
        , data: {}
        , put: function (set, texts) {
            var tag = RecTxt + ".i18n.put: ";
            var overwrite = set.overwrite; overwrite = overwrite == null || overwrite;
            var lang = set.lang; lang = i18n.alias[lang] || lang;
            if (!lang) throw new Error(tag + "set.lang?");
            var locale = i18n.locales[lang];
            if (!locale) { locale = {}; i18n.locales[lang] = locale };
            var exp = /^([\w\-]+):/, m;
            for (var i = 0; i < texts.length; i++) {
                var v = texts[i]; m = exp.exec(v);
                if (!m) { CLog(tag + "'key:'? " + v, 3, set); continue }
                var key = m[1], v = v.substr(key.length + 1);
                if (!overwrite && locale[key]) continue;
                locale[key] = v;
            }
        }
        , get: function () {
            return i18n.v_G.apply(null, arguments);
        }, v_G: function (key, args, lang) {
            args = args || [];
            lang = lang || i18n.lang; lang = i18n.alias[lang] || lang;
            var locale = i18n.locales[lang];
            var val = locale && locale[key] || "";
            if (!val && lang != "zh") {
                if (lang == "en") return i18n.v_G(key, args, "zh");
                return i18n.v_G(key, args, "en");
            }
            i18n.lastLang = lang;
            if (val == "=Empty") return "";
            return val.replace(/\{(\d+)(\!?)\}/g, function (v, a, b) {
                a = +a || 0; v = args[a - 1];
                if (a < 1 || a > args.length) { v = "{?}"; CLog("i18n[" + key + "] no {" + a + "}: " + val, 3) }
                return b ? "" : v;
            });
        }

        , $T: function () {
            return i18n.v_T.apply(null, arguments);
        }, v_T: function () {
            var a = arguments, key = "", args = [], isArgs = 0, tag = RecTxt + ".i18n.$T:";
            var exp = /^([\w\-]*):/, m;
            for (var i = 0; i < a.length; i++) {
                var v = a[i];
                if (i == 0) {
                    m = exp.exec(v); key = m && m[1];
                    if (!key) throw new Error(tag + "0 'key:'?");
                    v = v.substr(key.length + 1);
                }
                if (isArgs === -1) args.push(v);
                else if (isArgs) throw new Error(tag + " bad args");
                else if (v === 0) isArgs = -1;
                else if (IsNum(v)) {
                    if (v < 1) throw new Error(tag + " bad args");
                    isArgs = v;
                } else {
                    var lang = i == 1 ? "en" : i ? "" : "zh";
                    m = exp.exec(v); if (m) { lang = m[1] || lang; v = v.substr(m[1].length + 1); }
                    if (!m || !lang) throw new Error(tag + i + " 'lang:'?");
                    i18n.put({ lang: lang, overwrite: false }, [key + ":" + v]);
                }
            }
            if (!key) return "";
            if (isArgs > 0) return key;
            return i18n.v_G(key, args);
        }
    };
    var $T = i18n.$T; $T.G = i18n.get;
    $T("NonBrowser-1::非浏览器环境，不支持{1}", 1);
    $T("IllegalArgs-1::参数错误：{1}", 1);
    $T("NeedImport-2::调用{1}需要先导入{2}", 2);
    $T("NotSupport-1::不支持：{1}", 1);
    Recorder.TrafficImgUrl = "//ia.51.la/go1?id=20469973&pvFlag=1";
    var Traffic = Recorder.Traffic = function (report) {
        if (!isBrowser) return;
        report = report ? "/" + RecTxt + "/Report/" + report : "";
        var imgUrl = Recorder.TrafficImgUrl;
        if (imgUrl) {
            var data = Recorder.Traffic;
            var m = /^(https?:..[^\/#]*\/?)[^#]*/i.exec(location.href) || [];
            var host = (m[1] || "http://file/");
            var idf = (m[0] || host) + report;

            if (imgUrl.indexOf("//") == 0) {
                if (/^https:/i.test(idf)) {
                    imgUrl = "https:" + imgUrl;
                } else {
                    imgUrl = "http:" + imgUrl;
                };
            };
            if (report) {
                imgUrl = imgUrl + "&cu=" + encodeURIComponent(host + report);
            };

            if (!data[idf]) {
                data[idf] = 1;

                var img = new Image();
                img.src = imgUrl;
                CLog("Traffic Analysis Image: " + (report || RecTxt + ".TrafficImgUrl=" + Recorder.TrafficImgUrl));
            };
        };
    };



    if (WRec2) {
        CLog($T("8HO5::覆盖导入{1}", 0, RecTxt), 1);
        WRec2.Destroy();
    };
    Export[RecTxt] = Recorder;

}));
