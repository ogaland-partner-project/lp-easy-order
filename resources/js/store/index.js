import Vue from "vue";
import Vuex from "vuex";

import { msg, menu, common } from './modules';

import createPersistedState from "vuex-persistedstate";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        msg,
        menu,
        common
    },
    plugins: [createPersistedState({
        key: '@lp-easy-order',
        storage: window.localStorage,
    })]
});