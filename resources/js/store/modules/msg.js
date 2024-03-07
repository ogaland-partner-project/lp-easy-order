const getters = {
    getText(state) {
        return state.text;
    }
};

const state = {
    //初期状態を定義
    text: "",
    color: "primary",
    snackbar: false,
};

const mutations = {
    //stateをmutationsで変更する。
    setSnackbar: (state, { text, color }) => {
        state.snackbar = true;
        state.text = text;
        state.color = color;
    },
    unsetSnackbar: function(state) {
        state.snackbar = false;
    },
};

const actions = {
    //スナックバーを表示させる時の関数（第一引数に表示するテキストの内容)
    openSnackbar: ({ commit }, { text, color}) => {
        //mutationsを発動させる。テキストの内容を引数で持たせる
        //commit('mutatiuonsの関数名')でmutationsを発動させられる
        commit("setSnackbar", { text: text, color: color });
    },
    //スナックバーを非表示にする関数
    closeSnackbar: ({ commit }) => {
        commit("unsetSnackbar");
    },
};

export default {
    namespaced: true,
    getters,
    state,
    mutations,
    actions,
};