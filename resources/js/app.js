import { createApp } from "vue";

import AppFilters from "./components/filters/AppFilters.vue";

function newApp(container) {
  let vueApp = createApp({
    data: () => ({
      extra: {},
    }),
  });

  vueApp.component("AppFilters", AppFilters);
  
  vueApp.config.globalProperties.$t = function (key) {
    if (!Object.hasOwn(app.lang, key)) {
      console.warn(`Language key "${key}" not found.`);

      return key;
    }

    return app.lang[key]
  };
  vueApp.mount(container);
  return vueApp;
}

newApp("#vueApp");
window.vNewApp = newApp;
