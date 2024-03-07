const getters = {
    getNowMenuName(state) {
        return state.nowMenuName;
    }
};

const state = {
    nowMenuName: ''
};

const mutations = {
    //stateをmutationsで変更する。
    setNowMenuName: (state, payload) => {
        state.nowMenuName = payload;
    },
};

const actions = {
    //スナックバーを表示させる時の関数（第一引数に表示するテキストの内容)
    setNowMenuName: ({ commit }, payload) => {
        commit("setNowMenuName", payload);
    },
};

export default {
    namespaced: true,
    getters,
    state,
    mutations,
    actions,
};