<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/css/chat.css'); ?>">
<?php
$csrfToken = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;
$members = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
?>

<div id="wrapper" style="min-height:850px">
    <div class="content">
        <div id="app">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success mbot10 tw-flex tw-justify-between">
                        <p><?php echo _l('chat_message_note'); ?></p>
                        <span class="tw-cursor-pointer hideMessage"><i class="fa-solid fa-xmark"></i></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div v-if="errorMessage" class="alert alert-danger text-center fixed-top tw-flex tw-justify-between" role="alert" style="z-index: 1000;">
                        <p>{{ errorMessage }}</p>
                        <span class="tw-cursor-pointer hideMessage"><i class="fa-solid fa-xmark"></i></span>
                    </div>
                </div>
            </div>
            <div class="row app-one">
                <div class="col-sm-3 side">
                    <div class="side-one">
                        <div class="row heading">
                            <div class="col-sm-3 col-xs-3 heading-avatar profile">
                                <div class="heading-avatar-icon">
                                    <img src="<?= !empty(get_option("wac_profile_picture_url")) ? get_option("wac_profile_picture_url") : base_url('assets/images/user-placeholder.jpg') ?>">
                                </div>
                            </div>
                            <div class="col-sm-8 col-xs-6 tw-mt-2" v-if="wb_selectedinteraction && typeof wb_selectedinteraction === 'object'">
                                <p><?php echo _l('from'); ?> {{ wb_selectedinteraction.wa_no }}</p>
                            </div>

                        </div>
                        <div class="row searchBox">
                            <div class="col-sm-12 searchBox-inner">
                                <div class="form-group has-feedback">
                                    <select class="form-control" v-model="wb_selectedWaNo" v-on:change="wb_filterInteractions" id="wb_selectedWaNo">
                                        <option v-for="(interaction, index) in wb_uniqueWaNos" :key="index" :value="interaction.wa_no" :selected="wb_selectedWaNo === 'interaction.wa_no'">
                                            {{ interaction.wa_no }}
                                        </option>
                                        <option value="*"><?php echo _l('all_chat'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row searchBox">
                            <div class="col-sm-12 searchBox-inner">
                                <div class="form-group has-feedback">
                                    <input id="wb_searchText" v-model="wb_searchText" type="text" class="form-control" name="wb_searchText" placeholder="Search">
                                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row sideBar">
                            <div class="row sideBar-body tw-flex tw-items-center tw-justify-center" v-for="(interaction, index) in wb_displayedInteractions" :key="interaction.id" v-on:click="wb_selectinteraction(interaction.id)" :class="{'selected-interaction': wb_selectedinteraction && wb_selectedinteraction.id === interaction.id}">
                                <div class="col-sm-2 col-xs-2 sideBar-avatar tw-flex tw-justify-center">
                                    <div :class="[interaction.type === 'lead' ? 'avatar-icon leads' : 'avatar-icon']">
                                        <p class="text-dark tw-font-bold tw-mt-2 tw-text-base no-margin">{{
                                            wb_getAvatarInitials(interaction.name) }}</p>
                                    </div>
                                </div>
                                <div class="col-sm-10 col-xs-10 sideBar-main">
                                    <div class="row">
                                        <div class="col-sm-6 col-xs-6 sideBar-name">
                                            <span class="name-meta">{{ interaction.name }} </span>
                                            <br>
                                            <u>{{ interaction.type }}</u>
                                            <br>
                                            <span v-html="wb_truncateText(interaction.last_message, 2)"></span>
                                        </div>
                                        <div class="col-sm-6 col-xs-6 pull-right sideBar-time tw-flex tw-flex-col">
                                            <span class="time-meta pull-right">{{ wb_formatTime(interaction.time_sent)
                                                }}</span>
                                            <div class="tw-flex tw-gap-2 tw-items-center tw-justify-end">
                                                <span class="badge pull-right tw-my-2" v-if="wb_countUnreadMessages(interaction.id) > 0"> {{
                                                    wb_countUnreadMessages(interaction.id) }} </span>
                                                <span v-on:click="wb_deleteInteraction(interaction.id)" class="delete dele-icn hide">
                                                    <i class="fa-solid pull-right fa-trash text-danger mright10 mtop1" data-toggle="tooltip" data-placement="top" title="<?php echo _l('remove_chat'); ?>"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-9 conversation no-padding">
                    <div class="row heading" v-if="wb_selectedinteraction && typeof wb_selectedinteraction === 'object'">
                        <div :class="[wb_selectedinteraction.type ? 'col-sm-2 col-xs-3 heading-avatar leads' : 'col-sm-2 col-xs-3 heading-avatar']">
                            <div class="heading-avatar-icon">
                                <p class="text-dark tw-font-bold tw-mt-2 no-margin">{{
                                    wb_getAvatarInitials(wb_selectedinteraction.name) }}</p>
                            </div>
                        </div>
                        <div class="col-sm-5 col-xs-5 heading-name">
                            <div class="row tw-flex tw-items-center">
                                <div class="col-md-5">
                                    <span class="heading-name-meta">
                                        <div class="tw-font-semibold">{{ wb_selectedinteraction.name }}</div>
                                        <div class="text-dark text-small"><i class="fa fa-phone"></i> +{{
                                            wb_selectedinteraction.receiver_id }}</div>
                                    </span>
                                </div>
                                <?php if (is_admin()) { ?>
                                    <div class="col-md-6 tw-flex tw-gap-3 tw-items-center tw-justify-center">
                                        <div v-if="wb_selectedinteraction.agent_name.agent_name" class="tw-flex tw-items-center">
                                            <span class="tw-mr-1"><i class="fa-lg fa-regular fa-user tw-mr-2 fa-lg" data-toggle="tooltip" data-placement="top" title="<?php echo _l('support_agent'); ?>"></i></span>
                                            <div class="tw-items-center ltr:tw-space-x-2 tw-inline-flex">
                                                <div class="tw-flex -tw-space-x-1" v-html="wb_selectedinteraction.agent_icon"></div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-secondary btn-sm" v-on:click="wb_initAgent">
                                            <i class="fa-solid fa-user-pen fa-lg" data-toggle="tooltip" data-placement="top" title="<?php echo _l('change_support_agent'); ?>"></i>
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-sm-5 col-xs-4 heading-dot pull-right tw-mt-2">
                            <span class="pull-right last-msg-time" v-if="wb_selectedinteraction.last_msg_time" v-html="wb_alertTime(wb_selectedinteraction.last_msg_time)"></span>
                        </div>

                    </div>
                    <?php if (is_admin()) { ?>
                        <div class="modal fade" id="AgentModal" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo _l('modal_title'); ?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <?= render_select(
                                            'assigned[]',
                                            $members,
                                            ['staffid', ['firstname', 'lastname']],
                                            '',
                                            '',
                                            ['data-width' => '100%', 'multiple' => true, 'data-actions-box' => true],
                                            [],
                                            '',
                                            '',
                                            false
                                        ); ?>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _l('close_btn'); ?></button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="wb_handleAssignedChange"><?php echo _l('save_btn'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="row message" id="conversation" ref="wb_chatContainer">
                        <div class="row message-body" v-if="wb_selectedinteraction && wb_selectedinteraction.messages">
                            <div v-for="(message, index) in wb_selectedinteraction.messages" :key="index" class="msg mbot20">
                                <div v-if="wb_shouldShowDate(message, wb_selectedinteraction.messages[index - 1])" class="text-center tw-my-2 msg ">
                                    <span class="tw-p-3 tw-bg-white tw-rounded-full">{{
                                        getDate(message.time_sent) }}</span>
                                </div>
                                <div :class="[message.sender_id === wb_selectedinteraction.wa_no ? 'text-left tw-mb-4' : 'text-left tw-mb-4',]">
                                    <div :class="[
                                    'rounded p-3',
                                    message.sender_id === wb_selectedinteraction.wa_no ? 'sender' : 'receiver',
                                    message.staff_id == 0 && message.sender_id === wb_selectedinteraction.wa_no ? 'custom_message' : '' ,
                                    message.type === 'text' && message.message.length > 50 ? 'max-width-60' : ''
                                    ]" v-bind="message.sender_id === wb_selectedinteraction.wa_no ? {
                                         'data-toggle': 'tooltip',
                                         'data-title': message.staff_name,
                                         'data-original-title': message.staff_name,
                                         'title': message.staff_name,
                                         'data-placement': 'left'
                                       } : {}">
                                        <template v-if="message.type === 'interactive'">
                                            <p class="text-dark font-size-14">{{ message.message }}</p>
                                        </template>

                                        <template v-if="message.type === 'text'">
                                            <p class="text-dark font-size-14" v-html="message.message"></p>
                                        </template>

                                        <template v-if="message.type === 'button'">
                                            <p class="text-dark font-size-14" v-html="message.message"></p>
                                        </template>

                                        <template v-if="message.type === 'reaction'">
                                            <p class="text-dark font-size-14" v-html="message.message"></p>
                                        </template>

                                        <template v-else-if="message.type === 'image'">
                                            <a :href="message.asset_url" data-lightbox="image-group">
                                                <img :src="message.asset_url" alt="Image" class="img-responsive img-rounded" style="max-width: 200px; max-height: 112px;">
                                            </a>
                                            <p class="small mt-2" v-if="message.caption">{{ message.caption }}</p>
                                        </template>

                                        <template v-else-if="message.type === 'video'">
                                            <video :src="message.asset_url" controls class="img-responsive img-rounded" style="max-width: 200px; max-height: 112px;"></video>
                                            <p class="small mt-2" v-if="message.message">{{ message.message }}</p>
                                        </template>

                                        <template v-else-if="message.type === 'document'">
                                            <a :href="message.asset_url" target="_blank" class="btn btn-default">Download Document</a>
                                        </template>

                                        <template v-else-if="message.type === 'audio'">
                                            <audio controls style="max-width: 200px; max-height: 200px;">
                                                <source :src="message.asset_url" type="audio/mpeg">
                                            </audio>
                                            <p class="small mt-2" v-if="message.message">{{ message.message }}</p>
                                        </template>

                                        <div class="clearfix mt-2 ">
                                            <span :class="['text-muted' ,{ 'pull-left  mright15': message.sender_id === wb_selectedinteraction.wa_no }]" style="font-size: 12px;">
                                                {{ wb_getTime(message.time_sent) }}
                                            </span>
                                            <span class="ml-2 pull-right" v-if="message.sender_id === wb_selectedinteraction.wa_no">
                                                <i v-if="message.status === 'sent'" class="fa fa-check text-muted" title="Sent"></i>
                                                <i v-else-if="message.status === 'delivered'" class="fa fa-check-double text-muted" title="Delivered"></i>
                                                <i v-else-if="message.status === 'read'" class="fa fa-check-double text-primary" title="Read"></i>
                                                <i v-else-if="message.status === 'failed'" class="fa fa-exclamation-circle text-danger" title="Failed"></i>
                                                <i v-else-if="message.status === 'deleted'" class="fa fa-trash text-danger" title="Deleted"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br><br> <!-- Using <br> for line breaks instead of </br> -->
                        </div>
                    </div>
                    <div v-if="wb_imageAttachment || wb_videoAttachment || wb_documentAttachment" class="selected-files">
                        <div v-if="wb_imageAttachment" class="selected-file">
                            <i class="fa fa-times close-icons text-danger" v-on:click="wb_removeImageAttachment"></i>
                            <img :src="wb_imagePreview" alt="Selected Image" style="max-width: 100px; max-height: 100px;" />

                            <span class="padding-10">{{ wb_imageAttachment.name }}</span>

                        </div>
                        <div v-if="wb_videoAttachment" class="selected-file">
                            <i class="fa fa-times close-icons text-danger" v-on:click="wb_removeVideoAttachment"></i>
                            <video :src="wb_videoPreview" controls></video>
                            <span class="padding-10">{{ wb_videoAttachment.name }}</span>

                        </div>
                        <div v-if="wb_documentAttachment" class="selected-file">
                            <i class="fa fa-times close-icons text-danger" v-on:click="wb_removeDocumentAttachment"></i>
                            <i class="fa fa-file"></i>
                            <span class="padding-10">{{ wb_documentAttachment.name }}</span>

                        </div>
                    </div>
                    <div class="row reply" v-if="wb_selectedinteraction && wb_selectedinteraction.messages">
                        <div class="reply-section">

                            <form v-on:submit.prevent="wb_sendMessage">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default">
                                                <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/reference/media" target="_blank" class="tw-text-black/100"><i class="fa-regular fa-circle-question font-medium" data-toggle="tooltip" data-title="<?= _l('chat_media_info') ?>" data-placement="top" data-original-title="" title=""></i></a>
                                            </span>
                                        </span>
                                        <input type="text" v-model="wb_newMessage" class="form-control" placeholder="Type your message..." id="wb_newMessage">
                                        <span class="input-group-btn">
                                            <input type="file" id="imageAttachmentInput" ref="imageAttachmentInput" v-on:change="wb_handleImageAttachmentChange" accept="<?= wb_get_allowed_extension()['image']['extension'] ?>" class="hidden">
                                            <label for="imageAttachmentInput" class="btn btn-default">
                                                <i class="fa fa-image" data-toggle="tooltip" data-title="<?= _l('send_image') ?>" data-placement="top" data-original-title="" title=""></i>
                                            </label>

                                            <input type="file" id="videoAttachmentInput" ref="videoAttachmentInput" v-on:change="wb_handleVideoAttachmentChange" accept="<?= wb_get_allowed_extension()['video']['extension'] ?>" class="hidden">
                                            <label for="videoAttachmentInput" class="btn btn-default">
                                                <i class="fa fa-video" data-toggle="tooltip" data-title="<?= _l('send_video') ?>" data-placement="top" data-original-title="" title=""></i>
                                            </label>

                                            <input type="file" id="documentAttachmentInput" ref="documentAttachmentInput" v-on:change="wb_handleDocumentAttachmentChange" accept="<?= wb_get_allowed_extension()['document']['extension'] ?>" class="hidden">
                                            <label for="documentAttachmentInput" class="btn btn-default">
                                                <i class="fa fa-file" data-toggle="tooltip" data-title="<?= _l('send_document') ?>" data-placement="top" data-original-title="" title=""></i>
                                            </label>

                                            <button v-on:click="wb_toggleRecording" type="button" class="btn btn-default btn-custom">
                                                <span v-if="!wb_recording" class="fa fa-microphone" data-toggle="tooltip" data-title="<?= _l('record_audio') ?>" data-placement="top" data-original-title="" title=""></span>
                                                <span v-else class="fas fa-stop rounded-full p-2"></span>
                                            </button>

                                            <button v-if="wb_showSendButton || wb_audioBlob" type="submit" class="btn btn-default">
                                                <i class="fa fa-paper-plane"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/chat.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/vue.min.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/axios.min.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/recorder-core.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/purify.min.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/mp3-engine.js'); ?>"></script>
<script src="<?php echo module_dir_url(WHATSBOT_MODULE, 'assets/js/mp3.js'); ?>"></script>


<script>
    "use strict";
    $(document).on('click', '.hideMessage', function() {
        $(this).parent().addClass('hide');
    });

    $(function() {
        $(document).on('mouseenter', '.sideBar-body', function() {
            $(this).find('.dele-icn').removeClass('hide');
        });

        $(document).on('mouseleave', '.sideBar-body', function() {
            $(this).find('.dele-icn').addClass('hide');
        });
    })

    new Vue({
        el: '#app',
        data() {
            return {
                interactions: [],
                previousCounts: {},
                wb_agentId: '',
                wb_selectedStaffId: '',
                wb_selectedinteractionIndex: null,
                wb_selectedinteraction: null,
                wb_selectedinteractionMobNo: null,
                wb_selectedinteractionSenderNo: null,
                wb_newMessage: '',
                wb_imageAttachment: null,
                wb_videoAttachment: null,
                wb_documentAttachment: null,
                wb_imagePreview: '',
                wb_videoPreview: '',
                wb_csrfToken: '<?php echo $csrfToken; ?>',
                wb_recording: false,
                wb_audioBlob: null,
                wb_recordedAudio: null,
                errorMessage: '',
                wb_searchText: '',
                wb_login_staff_id: '<?= get_staff_user_id(); ?>',
                wb_selectedWaNo: '<?= get_option("wac_default_phone_number") ?>', // Define wb_selectedWaNo variable
                wb_filteredInteractions: [], // Define wb_filteredInteractions to store filtered interactions
                wb_displayedInteractions: [],
            };
        },
        methods: {
            wb_selectinteraction(id) {
                $.ajax({
                    url: `${admin_url}whatsbot/chat_mark_as_read`,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'interaction_id': id
                    },
                })
                const index = this.interactions.findIndex(interaction => interaction.id === id);
                if (index !== -1) {
                    this.wb_selectedinteractionIndex = index;
                    this.wb_selectedinteraction = this.interactions[index];
                    this.wb_selectedinteractionMobNo = this.wb_selectedinteraction['receiver_id'];
                    this.wb_selectedinteractionSenderNo = this.wb_selectedinteraction['wa_no'];
                    this.wb_scrollToBottom();
                }
            },
            wb_initAgent() {
                const agentId = this.wb_selectedinteraction.agent.agent_id;
                this.selectedStaffId = agentId;
                $('#AgentModal').modal('show');
                setTimeout(function() {
                    $('#AgentModal').find('select[name="assigned[]"]').val(agentId);
                    $('#AgentModal').find('select[name="assigned[]"]').trigger('change');
                }, 100);
            },
            wb_handleAssignedChange(event) {
                const id = this.wb_selectedinteraction ? this.wb_selectedinteraction.id : null;
                const staffId = $('select[name="assigned[]"]').val();
                $.ajax({
                    url: `${admin_url}whatsbot/assign_staff`,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'staff_id': staffId,
                        'interaction_id': id
                    },
                })
                this.wb_selectinteraction(id);
            },

            wb_delete_agent_icon() {
                if (confirm_delete()) {
                    const id = this.wb_selectedinteraction ? this.wb_selectedinteraction.id : null;
                    $.ajax({
                        url: `${admin_url}whatsbot/remove_agent`,
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            'staff_id': $(this).data('agent_id'),
                            'interaction_id': id
                        },
                    })
                    this.wb_selectinteraction(id);
                }
            },

            wb_deleteInteraction(id) {
                if (confirm_delete()) {
                    $.ajax({
                        url: `${admin_url}whatsbot/delete_chat`,
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            'interaction_id': id
                        },
                    }).done(function(res) {
                        if (res) {
                            alert_float('danger', "<?= _l('deleted', _l('chat')); ?>");
                        }
                    });
                }
            },

            async wb_sendMessage() {
                if (!this.wb_selectedinteraction) return;
                const wb_formData = new FormData();
                wb_formData.append('id', this.wb_selectedinteraction.id);
                wb_formData.append('to', this.wb_selectedinteraction.receiver_id);
                wb_formData.append('csrf_token_name', this.wb_csrfToken);

                const MAX_MESSAGE_LENGTH = 2000;
                if (this.wb_newMessage.length > MAX_MESSAGE_LENGTH) {
                    this.wb_newMessage = this.wb_newMessage.substring(0, MAX_MESSAGE_LENGTH);

                }
                // Add message if it exists
                if (this.wb_newMessage.trim()) {
                    wb_formData.append('message', DOMPurify.sanitize(this.wb_newMessage));
                }

                // Handle image attachment
                if (this.wb_imageAttachment) {
                    wb_formData.append('image', this.wb_imageAttachment);
                }

                // Handle video attachment
                if (this.wb_videoAttachment) {
                    wb_formData.append('video', this.wb_videoAttachment);
                }

                // Handle document attachment
                if (this.wb_documentAttachment) {
                    wb_formData.append('document', this.wb_documentAttachment);
                }

                // Handle audio attachment
                if (this.wb_audioBlob) {
                    wb_formData.append('audio', this.wb_audioBlob, 'audio.mp3');
                }
                try {
                    const wb_response = await axios.post('<?php echo admin_url('whatsbot/whatsapp_webhook/send_message'); ?>', wb_formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                    // Clear attachments
                    this.wb_newMessage = '';
                    this.wb_imageAttachment = null;
                    this.wb_videoAttachment = null;
                    this.wb_documentAttachment = null;
                    this.wb_audioBlob = null;
                    this.wb_imagePreview = '';
                    this.wb_videoPreview = '';
                    this.wb_filterInteractions();
                    this.wb_selectinteraction(this.wb_selectedinteraction.id);
                    this.errorMessage = '';
                    this.wb_scrollToBottom();
                    this.wb_selectedinteractionIndex = 0;
                } catch (error) {
                    const wb_rawErrorMessage = error.response && error.response.data ? error.response.data : 'An error occurred. Please try again.';
                    // Define regular expressions to match the desired parts of the HTML error message
                    const wb_typeRegex = /<p>Type: (.+)<\/p>/;
                    const wb_messageRegex = /<p>Message: (.+)<\/p>/;

                    // Extract the type and message from the HTML error message
                    const wb_typeMatch = wb_rawErrorMessage.match(wb_typeRegex);
                    var wb_messageMatch = wb_rawErrorMessage.match(wb_messageRegex);

                    if (typeof(wb_messageMatch[1] == 'object')) {
                        wb_messageMatch[1] = JSON.parse(wb_messageMatch[1]);
                        wb_messageMatch[1] = wb_messageMatch[1].error.message;
                    }

                    const wb_getTypeText = wb_typeMatch ? wb_typeMatch[1] : '';
                    const wb_getMessageText = wb_messageMatch ? wb_messageMatch[1] : '';

                    // Construct the error message by concatenating the extracted text content
                    const errorMessage = wb_getTypeText.trim() + '\n' + wb_getMessageText.trim();
                    this.errorMessage = errorMessage;
                }
            },

            wb_clearMessage() {
                this.wb_newMessage = '';
                this.attachment = null;
                this.wb_audioBlob = null;
            },

            wb_handleAttachmentChange(event) {
                const files = event.target.files;
                this.attachment = files[0];
            },

            async wb_fetchinteractions() {
                try {
                    const staff_id = this.wb_login_staff_id;
                    const wb_response = await fetch('<?php echo admin_url('whatsbot/interactions'); ?>');
                    const data = await wb_response.json();
                    const enable_supportagent = "<?= get_option('enable_supportagent') ?>";

                    if (data && data.interactions) {

                        const isAdmin = <?php echo is_admin() ? 'true' : 'false'; ?>;

                        if (!isAdmin && enable_supportagent == 1) {
                            this.interactions = data.interactions.filter(interaction => {

                                const chatagent = interaction.agent;
                                if (chatagent) {

                                    const agentIds = Array.isArray(chatagent.agent_id) ? chatagent.agent_id : [chatagent.agent_id];
                                    console.log('kaka', agentIds)
                                    const assignIds = Array.isArray(chatagent.assign_id) ? chatagent.assign_id : [chatagent.assign_id];

                                    // Check if `staff_id` is included in either `agentIds` or `assignIds`
                                    return agentIds.includes(staff_id) || assignIds.includes(staff_id);
                                }
                                return false;
                            });
                        } else {

                            this.interactions = data.interactions;
                        }

                    } else {
                        this.interactions = [];
                    }
                    this.wb_filterInteractions();
                    this.wb_updateSelectedInteraction();
                } catch (error) {
                    console.error('Error fetching interactions:', error);
                }
            },
            wb_updateSelectedInteraction() {
                const wb_new_index = this.interactions.findIndex(interaction => interaction.receiver_id === this.wb_selectedinteractionMobNo && interaction.wa_no === this.wb_selectedinteractionSenderNo);
                this.wb_selectedinteraction = this.interactions[wb_new_index];
            },

            wb_getTime(timeString) {
                return timeString.split(' ')[1]; // Split the string and return the time part
            },

            getDate(dateString) {
                const wb_date = new Date(dateString);
                const wb_options = {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                };
                return wb_date.toLocaleDateString('en-GB', wb_options).replace(' ', '-').replace(' ', '-');
            },

            wb_shouldShowDate(currentMessage, previousMessage) {
                if (!previousMessage) return true;
                return this.getDate(currentMessage.time_sent) !== this.getDate(previousMessage.time_sent);
            },

            wb_scrollToBottom() {
                this.$nextTick(() => {
                    const wb_chatContainer = this.$refs.wb_chatContainer;
                    if (wb_chatContainer) {
                        wb_chatContainer.scrollTop = wb_chatContainer.scrollHeight;
                    }
                });
            },

            wb_getAvatarInitials(name) {
                const wb_words = name.split(' ');
                const wb_initials = wb_words.slice(0, 2).map(word => word.charAt(0)).join('');
                return wb_initials.toUpperCase();
            },
            playNotificationSound() {
                var enableSound = "<?= get_option('enable_wtc_notification_sound') ?>";

                if (enableSound == 1) {
                    var audio = new Audio('<?= module_dir_url('whatsbot', 'assets/audio/whatsapp_notification.mp3') ?>');
                    audio.play();
                }
            },
            wb_countUnreadMessages(interactionId) {
                const interaction = this.interactions.find(inter => inter.id === interactionId);
                if (interaction) {
                    return interaction.messages.filter(message => message.is_read == 0).length;
                }
                return 0;
            },

            async wb_toggleRecording() {
                if (!this.wb_recording) {
                    // Start wb_recording
                    this.wb_startRecording();
                } else {
                    // Stop wb_recording
                    this.wb_stopRecording();
                }
            },
            wb_startRecording() {
                // Initialize Recorder.js if not already initialized
                if (!this.recorder) {
                    this.recorder = new Recorder({
                        type: "mp3",
                        sampleRate: 16000,
                        bitRate: 16,
                        onProcess: (buffers, powerLevel, bufferDuration, bufferSampleRate) => {

                        }
                    });
                }
                this.recorder.open((stream) => {
                    this.wb_recording = true;
                    this.recorder.start();
                }, (err) => {
                    console.error("Failed to start wb_recording:", err);
                });
            },

            wb_stopRecording() {
                if (this.recorder && this.wb_recording) {
                    this.recorder.stop((blob, duration) => {
                        this.recorder.close();
                        this.wb_recording = false;
                        this.wb_audioBlob = blob;
                        this.wb_sendMessage();
                        this.wb_recordedAudio = URL.createObjectURL(blob);
                    }, (err) => {
                        console.error("Failed to stop wb_recording:", err);

                    });
                }
            },
            wb_handleImageAttachmentChange(event) {
                this.wb_imageAttachment = event.target.files[0];
                this.wb_imagePreview = URL.createObjectURL(this.wb_imageAttachment);
            },
            wb_handleVideoAttachmentChange(event) {
                this.wb_videoAttachment = event.target.files[0];
                this.wb_videoPreview = URL.createObjectURL(this.wb_videoAttachment);
            },
            wb_handleDocumentAttachmentChange(event) {
                this.wb_documentAttachment = event.target.files[0];
            },
            wb_removeImageAttachment() {
                this.wb_imageAttachment = null;
                this.wb_imagePreview = '';
            },
            wb_removeVideoAttachment() {
                this.wb_videoAttachment = null;
                this.wb_videoPreview = '';
            },
            wb_removeDocumentAttachment() {
                this.wb_documentAttachment = null;
            },
            wb_formatTime(timestamp) {
                const currentDate = new Date();
                const messageDate = new Date(timestamp);
                const diffInMs = currentDate - messageDate;
                const diffInHours = diffInMs / (1000 * 60 * 60);

                if (diffInHours < 24) {
                    // Less than 24 hours, display time
                    const hour = messageDate.getHours();
                    const minute = messageDate.getMinutes();
                    const period = hour < 12 ? 'AM' : 'PM';
                    const formattedHour = hour % 12 || 12;
                    return `${formattedHour}:${minute < 10 ? '0' + minute : minute} ${period}`;
                } else {
                    // More than 24 hours, display wb_date in dd-mm-yy format
                    const day = messageDate.getDate();
                    const month = messageDate.getMonth() + 1;
                    const year = messageDate.getFullYear() % 100; // Get last two digits of the year
                    return `${day}-${month < 10 ? '0' + month : month}-${year}`;
                }
            },
            wb_alertTime(lastMsgTime) {
                if (lastMsgTime) {
                    const currentDate = new Date();
                    const messageDate = new Date(lastMsgTime);
                    const diffInMs = currentDate - messageDate;
                    const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60)); // Round down to nearest hour
                    const diffInMinutes = Math.floor((diffInMs % (1000 * 60 * 60)) / (1000 * 60)); // Calculate remaining minutes

                    // Check if the difference is less than 24 hours
                    if (diffInHours < 24) {
                        // Calculate remaining time within 24 hours
                        const remainingHours = 23 - diffInHours; // Subtract one hour from 24
                        const remainingMinutes = 60 - diffInMinutes;
                        return `Reply within ${remainingHours} hours and ${remainingMinutes} minutes`;
                    } else {
                        return "<span class='text-danger'><?= _l('replay_message') ?></span>";
                    }
                } else {
                    return lastMsgTime;
                }
            },
            wb_stripHTMLTags(str) {
                return str.replace(/<\/?[^>]+(>|$)/g, "");
            },
            wb_truncateText(text, wordLimit) {
                const strippedText = this.wb_stripHTMLTags(text);
                const wb_words = strippedText.split(' ');
                if (wb_words.length > wordLimit) {
                    return wb_words.slice(0, wordLimit).join(' ') + '...';
                }
                return text;
            },
            wb_filterInteractions() {
                let filtered = this.interactions;

                if (this.wb_selectedWaNo !== "*") {
                    filtered = filtered.filter(interaction => interaction.wa_no === this.wb_selectedWaNo);
                }
                this.wb_filteredInteractions = filtered;
                this.wb_searchInteractions(); // Call wb_searchInteractions to apply the search text filter
            },

            wb_searchInteractions() {
                if (this.wb_searchText) {
                    this.wb_displayedInteractions = this.wb_filteredInteractions.filter(interaction =>
                        interaction.name.toLowerCase().includes(this.wb_searchText.toLowerCase())
                    );
                } else {
                    this.wb_displayedInteractions = this.wb_filteredInteractions;
                }
            },

            wb_markInteractionAsRead(interactionId) {
                // Immediately update the UI to reflect the interaction as read
                const interaction = this.interactions.find(interaction => interaction.id === interactionId);
                if (interaction) {
                    interaction.read = true; // Assuming there's a 'read' property in your interaction object
                }

                // Send a POST request to mark the interaction as read
                fetch('<?php echo admin_url('whatsbot/whatsapp_webhook/mark_interaction_as_read'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            interaction_id: interactionId,
                            csrf_token_name: this.wb_csrfToken
                        }),
                    })
                    .then(wb_response => {
                        if (!wb_response.ok) {
                            throw new Error('Network wb_response was not ok');
                        }
                        return wb_response.json();
                    })
                    .catch(error => {
                        console.error('Error marking interaction as read:', error);
                        // Revert the UI change if there's an error
                        if (interaction) {
                            interaction.read = false;
                        }
                    });
            },
        },

        watch: {
            wb_displayedInteractions(newInteractions) {
                newInteractions.forEach(interaction => {
                    const previousCount = this.previousCounts[interaction.id] || 0;
                    const currentCount = this.wb_countUnreadMessages(interaction.id);

                    if (currentCount > previousCount) {
                        this.playNotificationSound();
                    }

                    this.$set(this.previousCounts, interaction.id, currentCount);
                });
            }
        },

        created() {
            this.wb_fetchinteractions();
            setInterval(() => {
                this.wb_fetchinteractions();
            }, 5000);
            setInterval(() => {
                init_selectpicker();
            }, 2000);
        },
        computed: {
            wb_selectedInteraction() {
                return this.wb_selectedinteractionIndex !== null ? this.interactions[this.wb_selectedinteractionIndex] : null;
            },
            wb_showSendButton() {
                return this.wb_imageAttachment || this.wb_videoAttachment || this.wb_documentAttachment || this.wb_newMessage.trim();
            },
            wb_uniqueWaNos() {
                // Create a Set to store unique wa_no values
                const wb_uniqueWaNos = new Set();
                // Filter out interactions with duplicate wa_no values
                return this.interactions.filter(interaction => {
                    if (!wb_uniqueWaNos.has(interaction.wa_no)) {
                        wb_uniqueWaNos.add(interaction.wa_no);

                        return true;
                    }
                    return false;
                });
            }
        },
    });
</script>
