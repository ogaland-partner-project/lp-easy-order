import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";

Vue.use(Vuex);

/**
 * 認証情報保持
 */
const authModule = {
    namespaced: true,
    state: {
        //auth: authUser
        auth: {
            user_Id: 0,
            login_id: "",
            RecordFlag: false
        }
    },
    mutations: {
        /**
         *
         * @param {ログイン状態保持変数} state
         * @param {ログイン利用者情報} payload
         */
        SaveInfo(state, payload) {
            state.auth.user_Id = payload.user_Id;
            state.auth.login_id = payload.login_id;
            state.auth.RecordFlag = payload.RecordFlag;
        }
    },
    // actions: {

    // }
    getters: {
        GetUserID(state) {
            return state.user_id;
        },
        GetLoginID(state) {
            return state.login_id;
        },
        GetRecordFlag(state) {
            return state.RecordFlag;
        }
    }
};

export default new Vuex.Store({
    modules: {
        user: authModule
    },
    plugins: [createPersistedState({
        key: '@project@_login',
        paths: ['user'],
        storage: window.localStorage,
    }
    )]     
});
