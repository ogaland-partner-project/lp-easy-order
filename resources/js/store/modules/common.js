const state = {
    beforeRouting: "",
    // 選択したメニューにインデックス
    selectionMenu: 0,
    // ホーム画面で選択された商品名（各画面のヘッダーで表示する用）
    selectedProductName: '',
    // ホーム画面で選択された商品のステータス（ステータスにより操作を制御する画面がある）
    selectedProductStatus:null,
    // 表示中の画面が編集中かどうか
    checkLock:false,
    checkEdit:true,
};

const getters = {
    getBeforeRouting(state) {
        return state.beforeRouting;
    },
    getSelectionMenu(state) {
        return state.selectionMenu;
    },
    getSelectedProductName(state) {
        return state.selectedProductName;
    },
    getSelectedProductStatus(state) {
        return state.selectedProductStatus;
    },
    getCheckLock(state){
        return state.checkLock;
    },
    getCheckEdit(state){
        return state.checkEdit;
    }
};

const mutations = {
    setBeforeRouting: (state, payload) => {
        state.beforeRouting = payload
    },
    setSelectionMenu: (state, payload) => {
        state.selectionMenu = payload
    },
    setSelectedProductName: (state, payload) => {
        state.selectedProductName = payload
    },
    setSelectedProductStatus: (state, payload) => {
        state.selectedProductStatus = payload
    },
    setCheckLock: (state, payload) => {
        state.checkLock = payload
    },
    setCheckEdit: (state, payload) => {
        state.checkEdit = payload
    },
};

const actions = {
    setBeforeRouting: ({ commit }, payload) => {
        commit("setBeforeRouting", payload);
    },
    setSelectionMenu: ({ commit }, payload) => {
        commit("setSelectionMenu", payload);
    },
    setSelectedProductName: ({ commit }, payload) => {
        commit("setSelectedProductName", payload);
    },
    setSelectedProductStatus: ({ commit }, payload) => {
        commit("setSelectedProductStatus", payload);
    },
    setCheckLock: ({ commit }, payload) => {
        commit("setCheckLock", payload);
    },
    setCheckEdit: ({ commit }, payload) => {
        commit("setCheckEdit", payload);
    },
};

export default {
    namespaced: true,
    getters,
    state,
    mutations,
    actions,
};