<template>
    <div class="home_main">
        <v-data-table id="home-table" :headers="headers" :items="desserts" :search="searchText"
            :items-per-page="-1"
            :footer-props="{'disable-items-per-page': true}"
        >
            <template v-slot:top>
                <div class="d-flex align-center">
                    <div class="home_search_area">
                        <v-text-field
                            v-model="searchText"
                            append-icon="mdi-magnify"
                            label="キーワードを入力"
                            class="home_search_box"
                            hide-details
                            color="green"
                            clearable
                        ></v-text-field>
                    </div>
                    <v-spacer />
                    <div class="save_edit_area">
                        <v-btn @click="onClickNew">
                            <v-icon>fa-solid fa-pen</v-icon>新規作成
                        </v-btn>
                    </div>
                </div>
            </template>
            <template v-slot:header.product_code="prop">
                <span>{{prop.header.text}}</span>
                <v-tooltip top color="#8CCCC0">
                    <template v-slot:activator="{on,attrs}">
                        <v-icon v-bind="attrs" v-on="on" @click.native.stop>fa-solid fa-comment-dots</v-icon>
                    </template>
                    <span>サプリコード/品目コード 等</span>
                </v-tooltip>
            </template>
            <template v-slot:header.description="prop">
                <span>{{prop.header.text}}</span>
                <v-tooltip top color="#8CCCC0">
                    <template v-slot:activator="{on,attrs}">
                        <v-icon v-bind="attrs" v-on="on" @click.native.stop>fa-solid fa-comment-dots</v-icon>
                    </template>
                    <span>薬機修正/楽天用リニュ/生産者変更、用途が分かるように入力</span>
                </v-tooltip>
            </template>
            <template v-slot:header.editing="prop">
                <span>{{prop.header.text}}</span>
                <v-tooltip top color="#8CCCC0">
                    <template v-slot:activator="{on,attrs}">
                        <v-icon v-bind="attrs" v-on="on" @click.native.stop>fa-solid fa-comment-dots</v-icon>
                    </template>
                    <span>「編集中」と表示されている場合、該当する構成は現在編集中です</span>
                </v-tooltip>
            </template>
            <template v-slot:item="prop">
                <tr
                    class="home_table_row"
                    :class="{
                        'active-row': isActiveRow(prop.item.id),
                        'finished-row': isFinishedRow(prop.item.status)
                    }"
                    tabindex="0"
                    @click="onClickRow(prop.item)"
                    @blur="closeRightMenu"
                >
                    <td>{{prop.item.id}}</td>
                    <td>{{prop.item.product_name}}</td>
                    <td>{{prop.item.product_code}}</td>
                    <td>{{prop.item.description}}</td>
                    <td>
                        <span v-on:click.right.ctrl.alt.stop="destroy(prop.item.id)">
                            {{ prop.item.lock }}
                        </span>
                    </td>
                    <td style="display:flex; flex-flow:column; height: 100%;">
                        <span>{{statusName(prop.item.status)}}</span>
                        <v-progress-linear
                            :value="((prop.item.status+1)/statusList.length)*100"
                            height="5"
                            stream
                            color="deep-orange"
                            class="sinchoku_radius"
                        ></v-progress-linear>
                    </td>
                    <div class="home_right_menu" v-if="prop.item.rightmenu">
                        <div @click.stop="moveNext(prop.item)">
                            <v-icon>fa-solid fa-folder-open</v-icon>開く
                            <span>選択しているデータを開きます</span>
                        </div>
                        <div @click.stop="onCopyClick(prop.item)">
                            <v-icon>fa-solid fa-copy</v-icon>コピー
                            <span>選択している構成データを複製します</span>
                        </div>
                        <div @click.stop="onEditClick(prop.item)">
                            <v-icon>fa-solid fa-pen-to-square</v-icon>編集
                            <span>選択している構成データを編集します</span>
                        </div>
                        <div @click.stop="onDeleteClick(prop.item)">
                            <v-icon>fa-solid fa-trash-can</v-icon>削除
                            <span>選択している構成データを削除します</span>
                        </div>
                    </div>
                </tr>
            </template>
        </v-data-table>

        <new-dialog v-model="newDialog" :model="lpOrderModel" @close="search" />

        <!-- 構成コピー確認ダイアログ -->
        <copy-dialog v-model="copyDialog" :model="lpOrderModel" @close="search" />

        <!-- 構成削除確認ダイアログ -->
        <delete-dialog v-model="deleteDialog" :model="lpOrderModel" @close="search" />
    </div>
</template>

<script>
import { mapActions } from 'vuex';
import NewDialog from './NewDialog.vue';
import DeleteDialog from './DeleteDialog.vue';
import CopyDialog from './CopyDialog.vue';
import { statusList, getStatusName } from './CommonHome.js';
export default {
    components: {
        NewDialog,
        DeleteDialog,
        CopyDialog
    },

    data() {
        return {
            headers: [
                { text: "ID", value: "id" },
                { text: "商品名", value: "product_name" },
                { text: "コード", value: "product_code" },
                { text: "簡易説明", value: "description" },
                { text: "編集状況", value: "lock", width:'10%' },
                { text: "進捗", value: "status" }
            ],
            desserts: [],
            searchText: "",
            newDialog: false,               // 登録・更新のダイアログ
            copyDialog: false,
            deleteDialog: false,
            lpOrderModel: {},               // ダイアログに渡すモデル
            statusList,
            activeRowId: 0,                    // クリックされた行のidを保持（色変更のため）
        };
    },

    created() {
        this.setSelectionMenu(0);
        this.search();
    },

    computed: {
        statusName() {
            return (status) => {
                return getStatusName(status);
            }
        }
    },

    methods: {
        ...mapActions("common",["setSelectionMenu", "setSelectedProductName", "setSelectedProductStatus"]),

        // APIs----------------------------
        // 検索
        search: async function() {
            const result = await this.$axios({
                method: "GET",
                url: "/home"
            });
            const datas = result.data.dataArray;
            this.desserts = datas;
        },

        // 新規作成ボタン押下時
        onClickNew() {
            // ダイアログを開く
            this.newDialog = true;
            this.lpOrderModel = {
                product_name: "",
                product_code: "",
                description: "",
                editing: 0,
                status: 0,
                requirement_flag: 0
            };
        },

        // 右クリックメニューの編集選択時
        onEditClick(item) {
            this.closeRightMenu();
            // ダイアログを開く
            this.newDialog = true;
            this.lpOrderModel = { ...item };
        },

        /**
         * 次画面に遷移
         * - レベル別質問事項画面
         * @version 1.1.0 新規追加
         * @param {*} item
         */
        moveNext(item) {
            // レベル別質問事項画面に遷移
            this.setSelectedProductName(item.product_name);
            this.setSelectedProductStatus(item.status);
            this.setSelectionMenu(1);
            this.$router.push("/level_select/" + item.id);
        },

        // コピーボタン押下時
        onCopyClick(item) {
            this.closeRightMenu();
            this.lpOrderModel = { ...item };
            this.copyDialog=true;
        },

        // 削除ボタン押下時
        onDeleteClick(item) {
            this.closeRightMenu();
            this.deleteDialog=true;
            this.lpOrderModel = { ...item };
        },

        // 右クリックメニュー閉じる
        closeRightMenu() {
            // HACK 将来パフォーマンスを考えると要修正かも
            this.desserts.map((item) => {
                item.rightmenu = false;
                return item;
            });
        },

        /**
         * 行クリックイベント
         * - コンテキストメニューを表示
         *   - 開く/コピー/編集/削除
         * @version 1.1.0 右クリックイベントを廃止
         * @param {*} item
         */
        onClickRow(item) {
            item.rightmenu = true;
            this.activeRowId = item.id;
        },

        /**
         * クリック行のidを比較してCSSのクラスを充てる
         * - 選択行を分かりやすく表示するため
         * @version 1.1.0 新規追加
         * @param {*} id
         */
        isActiveRow(id) {
            return id == this.activeRowId ? true : false;
        },

        /**
         * ステータスが完了か判定し、CSSのクラスを当てる
         * @version 1.1.0 新規追加
         * @param {*} status
         */
        isFinishedRow(status) {
            return status == 5 ? true : false;
        },

        // 編集中スイッチの更新
        editing:async function(item){
            item.editing = Number(item.editing);
            const params = item;
            const result = await this.$axios({
                method: "PUT",
                url: "/home/" + params.id,
                data: params
            });
        },

        // 編集中項目の削除
        destroy:async function(id){
            const result = await this.$axios({
                method: "DELETE",
                url: "/lock/all/" + id,
            });
            this.search();
        },
    },
};
</script>
<style>
/* クリック行を目立たせるCSS */
#home-table .active-row {
    background-color: rgb(255, 152, 152) !important;
    color:white !important
}

#home-table .finished-row {
    background-color: rgb(203 203 203 / 80%) !important;
}
</style>
