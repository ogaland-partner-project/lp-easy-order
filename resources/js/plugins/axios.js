import axios from "axios";
import { Utils } from '@/plugins/utils.js';          // axiosの自作プラグイン読込
import { $WebMsg } from "@/_CommonJs/WebMsg.js";

const AxiosPlugin = {};

AxiosPlugin.install = function (Vue, { store, router }) {
    Vue.prototype.$axios = axios.create({
        baseURL: process.env.MIX_API_BASE_URL + "/api/lp_easy_order/",
        headers: {
            "Content-Type": "application/json",
        },
        responseType: "json",
        timeout: 600000,
    });

    // リクエストログ
    Vue.prototype.$axios.interceptors.request.use(
        function (config) {
            return config;
        },
        function (error) {
            return Promise.reject(error);
        }
    );

    // レスポンスログ
    Vue.prototype.$axios.interceptors.response.use(
        function (response) {
            //　メッセージキーが無い時はすぐ返す
            let selfMsg = Utils.getExistKey(response, 'data.normalMessage');
            if(selfMsg == undefined) return response;
            // メッセージキーがあるときはメッセージをセットして返す
            if(response.data.normalMessage && response.data.normalMessage != '正常終了') {
                store.dispatch("msg/openSnackbar", { text: response.data.normalMessage, color: "primary" });
            }
            // 想定内の例外時→スイートアラートでエラーメッセージ表示
            if(response.data.errorMessage != '') {
                $WebMsg.InfWarning("エラー発生！", response.data.errorMessage);
            }
            return response;
        },
        function (error) {
            console.error(error.response);
            let msg = '';
            let selfError = Utils.getExistKey(error, 'response.data.errorMessage');
            if(selfError == undefined) {
                msg = error.message;
            }else {
                msg = error.response.data.errorMessage;
            }
            // router.push('/onepick/error');
            store.dispatch("msg/openSnackbar", { text: msg, color: "error" });
            return Promise.reject(error);
        }
    );
};

export default AxiosPlugin;