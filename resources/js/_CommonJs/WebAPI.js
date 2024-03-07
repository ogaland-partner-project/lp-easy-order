import $ from "jquery";
import $axios from "axios";

/**
 *
 */
class WebAPI {
    /**
     * ユーザーID取得
     */
    GetUserId() {
        let UserId = $('meta[name="user_id"]').attr("content");
        return UserId;
    }
    /**
     * ログインID取得
     */
    GetLoginName() {
        let login_Name = $('meta[name="login_id"]').attr("content");
        return login_Name;
    }
    /**
     * API Tokenを取得
     */
    GetToken() {
        let token = $('meta[name="api-token"]').attr("content");
        return token;
    }

    /**
     * CSRF Tokenを取得
     */
    GetCSRF() {
        let token = $('meta[name="csrf-token"]').attr("content");
        return token;
    }

    /**
     * Login-URLの取得
     */
    GetLoginUrl() {
        let url = $('meta[name="login-url"]').attr("content");
        return url;
    }

    /**
     *
     * @param {*} apiUrl
     */
    GetAxios(apiUrl) {
        let _token = $('meta[name="api-token"]').attr("content");

        return $axios.get(apiUrl, {
            headers: {
                Authorization: "Bearer " + _token,
                "Content-type": "application/json"
            }
        });
    }

    /**
     *
     * @param {*} apiUrl
     * @param {*} param
     */
    PostAxios(apiUrl, param) {
        let _token = $('meta[name="api-token"]').attr("content");

        return $axios.post(apiUrl, param, {
            headers: {
                Authorization: "Bearer " + _token,
                "Content-type": "application/json"
            }
        });
    }
}

export let $WebAPI = new WebAPI();
