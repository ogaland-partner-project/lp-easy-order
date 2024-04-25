<template>
    <v-app>
        <!-- サイドメニュー -->
        <v-navigation-drawer
            app
            v-model="drawer"
            style="top: 0px;"
            :mini-variant.sync="mini"
            :expand-on-hover="expandOnHover"
            mini-variant-width="60"
        >
            <v-list nav>
                <div class="logo">
                    <img v-if="!mini" src="/storage/navi/logo.svg" />
                    <img v-else style="width:70%; margin-left:7px;" src="/storage/navi/icon.svg" />
                </div>
                <v-list-item-group mandatory active-class="navi_active" v-model="selectionMenu">
                    <v-list-item
                        link
                        v-for="(item, i) in menus"
                        :key="i"
                        @click="onMenuClick(item)"
                        class="navi_list_item"
                        :disabled="selectionMenu == 0 || lockId != null"
                    >
                        <div :class="item.class" :style="mini ? 'margin-left: 0 !important;':''">
                            <v-list-item-content>
                                <div class="nav-list-icon" :style="'color:' + (selectionMenu == 0 || lockId != null ? 'gainsboro':'')">
                                    <v-icon>{{ item.icon }}</v-icon>
                                </div>
                                <v-list-item-title
                                    v-if="!mini"
                                    :style="'color:' + ((selectionMenu == 0  || lockId != null) && selectionMenu != item.id -1 ? 'gainsboro':'')"
                                >{{ item.name }}
                                </v-list-item-title>
                            </v-list-item-content>
                        </div>
                    </v-list-item>
                </v-list-item-group>
            </v-list>
        </v-navigation-drawer>

        <!-- メイン -->
        <v-main :class="mainClass">
            <div class="main_aria">
                <div class="main_title d-flex">
                    <v-btn
                        icon
                        @click.stop="setNavigationDisplay"
                    >
                        <v-icon color="white">mdi-dots-vertical</v-icon>
                    </v-btn>
                    <v-icon>{{ pageTitleIcon }}</v-icon>
                    {{pageTitle}}
                    <v-snackbar id="snackbar" top absolute :timeout="timeout" v-model="$store.state.msg.snackbar" :color="$store.state.msg.color">
                        <strong>{{ $store.state.msg.text }}</strong>
                        <template v-slot:action="{ attrs, on }">
                            <v-btn color="white" text v-bind="attrs" v-on="on" @click="closeSnackbar">
                            Close
                            </v-btn>
                        </template>
                    </v-snackbar>
                    <div v-if="selectionMenu !== 0" class="ml-10">ID:{{ this.getSelectedProductId }}　{{ this.getSelectedProductName }}</div>
                    <div v-if="selectionMenu == 3" class="ml-10 page_sub_title">調べたことを自由に記入しましょう。</div>
                    <div style="margin-left: auto; display: flex;">
                        <div class="save_edit_area">
                            <v-btn @click="onEdit(true)" :loading="editLoading" v-if="!getCheckLock && selectionMenu != 0">
                                <v-icon>fa-solid fa-pen-to-square</v-icon>編集
                            </v-btn>
                        </div>
                        <div class="save_edit_area">
                            <v-btn @click="onSave" v-if="lockId != null">
                                <v-icon>fa-solid fa-pen-to-square</v-icon>保存
                            </v-btn>
                        </div>
                        <div class="all_delete_btn" v-if="lockId != null && selectionMenu != 1">
                            <v-btn @click="onClear" class="delete_btn_color"><v-icon style="font-size:20px; padding:0 5px;">fa-solid fa-trash-can</v-icon>シート情報クリア</v-btn>
                        </div>
                        <div class="cancel_btn_area cancel_btn_color">
                            <v-btn @click="onCancel" v-if="lockId != null">
                                <v-icon>fa-solid fa-xmark</v-icon>キャンセル
                            </v-btn>
                        </div>
                    </div>
                </div>
                <div class="main_conteaints">
                    <v-container fluid>
                        <v-form :disabled="selectionMenu != 0 && lockId == null">
                            <router-view
                                ref="childview"
                                :lockId="lockId"
                            />
                        </v-form>
                    </v-container>
                </div>
            </div>
        </v-main>
        <com-clear ref="clear" @delete_action="fn_clear"></com-clear>
        <com-cancel ref="cancel" @calncel_action="fn_cancel"></com-cancel>
    </v-app>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import { $WebMsg } from "@/_CommonJs/WebMsg.js";
import Swal from 'sweetalert2'
export default {
    data() {
        return {
            drawer: true,
            mini:false,
            expandOnHover:false,
            menus: [
                {
                    id: 1,
                    name: "ＨＯＭＥ",
                    link: "home",
                    class: "Home",
                    icon: "fa-solid fa-house"
                },
                {
                    id: 2,
                    name: "レベル別質問事項",
                    link: "level_select",
                    class: "LevelSelect",
                    icon: "fa-solid fa-comments"
                },
                {
                    id: 3,
                    name: "商品カルテ入力",
                    link: "item_karte",
                    class: "ItemKarte",
                    icon: "fa-regular fa-clipboard"
                },
                {
                    id: 4,
                    name: "基礎知識",
                    link: "basic_knowledge",
                    class: "BasicKnowledge",
                    icon: "fa-solid fa-book"
                },
                {
                    id: 5,
                    name: "他社比較入力",
                    link: "comparison_insert",
                    class: "ComparisonInsert",
                    icon: "fa-solid fa-scale-unbalanced-flip"
                },
                {
                    id: 6,
                    name: "他社構成比較",
                    link: "companies_comparison",
                    class: "CompaniesComparison",
                    icon: "fa-solid fa-swatchbook"
                },
                {
                    id: 7,
                    name: "構成の手順",
                    link: "constitution_process",
                    class: "ConstitutionProcess",
                    icon: "fa-solid fa-list-ol"
                },
                {
                    id: 8,
                    name: "構成案",
                    link: "constitution_plan",
                    class: "ConstitutionPlan",
                    icon: "fa-solid fa-list-ul"
                },
                {
                    id: 9,
                    name: "最終デザイン確認",
                    link: "final_design_confirmation",
                    class: "FinalDesignConfirmation",
                    icon: "fa-regular fa-image"
                }
            ],
            mainClass: "",
            pageTitle: "",
            pageTitleIcon: "",
            selectionMenu: 0, // メニューの選択したインデント番号
            id: "", // lp_order_id（※url params）
            timeout: 3000,
            lockId:null,
            editLoading:false,
        };
    },

    // 画面離脱、リロード防止処理の登録
    created(){
        window.addEventListener("beforeunload", this.handler)
    },
    destroyed:async function(){
        window.removeEventListener("beforeunload", this.handler)
    },

    mounted() {
        // sotreに保持されているメニューのインデックスから、メニューを疑似的に押下した処理を実装
        // ⇒F5でreloadされた際もメニューの選択状態を保持するため
        this.selectionMenu = this.getSelectionMenu;
        const menu = this.menus[this.selectionMenu];

        // 読み込み時にID指定があればセット
        this.id = this.$route.params.id;

        this.onMenuClick(menu);
    },

    computed: {
        // Vuex
        ...mapGetters("common", ["getSelectionMenu", "getSelectedProductName","getCheckLock","getCheckEdit", "getSelectedProductId"])
    },

    watch: {
        // アクティブなメニューのインデントをstoreに保存して、mount時に再設定
        // ⇒F5でreloadされた際もメニューの選択状態を保持するため
        // ⇒今回はactice-classを使っているから保持されない？
        selectionMenu: function(newVal, oldVal) {
            this.setSelectionMenu(newVal);
            if(newVal != 0){
                this.lockCheck(newVal);
            }
        },

        // スナックバーのクリア
        '$store.state.msg.snackbar': function() {
            this.timeout = 3000;
            if(this.$store.state.msg.text == '他ユーザーがこの画面を編集中です') {
                this.timeout = -1;
            };
        },

        // メニュー遷移を検知して、ナビゲーションの遷移に対応
        '$store.state.common.selectionMenu': function() {
            this.closeSnackbar();
            this.selectionMenu = this.getSelectionMenu;
            const menu = this.menus[this.selectionMenu];
            this.mainClass = "main-" + menu.class;
            this.pageTitle = menu.name;
            this.pageTitleIcon = menu.icon;
        }
    },

    methods: {
        ...mapActions("common", ["setSelectionMenu","setCheckLock","setCheckEdit"]),
        ...mapActions("msg", ["openSnackbar", "closeSnackbar"]),

        onMenuClick(item) {
            const lpOrderId = this.$route.params.id;
            if(this.$route.path === '/home'){
                this.$router.push('/home', () => {});
                this.mainClass = "main-Home";
                this.pageTitle = "ＨＯＭＥ";
                this.pageTitleIcon = "fa-solid fa-house";
                this.selectionMenu = 0;
                return;
            }
            if(item.id != 1 && (lpOrderId == "" || lpOrderId == void 0 || lpOrderId == null)) {
                $WebMsg.InfWarning("エラー", "Home画面でLPを選択してください");
                return;
            }
            let url = "/" + item.link + "/" + this.$route.params.id;
            if(item.id == 1) {
                // ホーム画面の場合はID不要
                url = "/" + item.link;
            }
            this.$router.push(url, () => {});
            this.mainClass = "main-" + item.class;
            this.pageTitle = item.name;
            this.pageTitleIcon = item.icon;
        },

        // 保存
        onSave() {
            this.$refs.childview.save().then((res)=>{
                if(res){
                    this.unlock();
                }
            });
        },

        // 編集ボタン押下時
        onEdit(check){
            this.editLoading = true
            this.lockCheck(this.selectionMenu).then((lock)=>{
                if(check) this.$refs.childview.search();
                if(!lock) this.lock();
                this.editLoading = false
            })
        },

        onClear(){
            this.$refs.clear.open();
        },

        // 画面の入力値全クリア
        fn_clear(){
            this.$refs.childview.fn_clear();
            this.unlock();
        },

        onCancel(){
            this.$refs.cancel.open();
        },

        // 編集ロックキャンセル(入力値はリセットされる)
        fn_cancel(){
            this.$refs.childview.search();
            this.unlock();
        },

        // ナビゲーションメニューの表示制御
        setNavigationDisplay() {
            if(this.$vuetify.breakpoint.xl || this.$vuetify.breakpoint.lg) {
                // PC or タブレット（横） → メニューミニ表示または通常表示
                this.mini= this.mini ? false:true;
                this.expandOnHover = this.mini ? true:false;
            }else {
                // タブレット（縦） or スマホ → メニュー表示または非表示
                this.mini = false;
                this.expandOnHover = false;
                this.drawer= this.drawer ? false:true;
            }
        },

        // ロックされているかどうか
        lockCheck:async function(menu_id){
            let param = {
                lp_order_id:this.$route.params.id,
                menu_id:menu_id
            }
            const result = await this.$axios({
                method: "POST",
                url: "/lock/check",
                data:param
            });
            this.setCheckLock(result.data.dataArray);
            return result.data.dataArray
        },

        // 編集ロック
        lock:async function(){
            let param = {
                lp_order_id:this.$route.params.id,
                menu_id:this.selectionMenu
            }
            const result = await this.$axios({
                method: "POST",
                url: "/lock",
                data:param
            });
            this.setCheckLock(true);
            this.setCheckEdit(false);
            this.lockId = result.data.dataArray;
        },

        // 編集ロック解除
        unlock:async function(){
            let param = {
                data:{
                    lp_order_id:this.$route.params.id,
                    menu_id:this.selectionMenu
                }
            }
            const result = await this.$axios({
                method: "DELETE",
                url: "/lock",
                data:param
            });
            this.setCheckLock(false);
            this.setCheckEdit(true);
            this.lockId = null
        },

        /**
         * 編集ロック時の画面離脱、リロード防止処理
         * 離脱防止アラートの離脱、キャンセルのどちらを選んだかを検知することが出来ないので
         * どちらにしても、編集ロックを一旦解除し、離脱しなかった場合はもう一度編集ロックを行う
         */
        handler:async function (event) {
            if(this.lockId != null){
                this.unlock();
                event.preventDefault();
                event.returnValue = "";
                // 画面に留まる場合のみ再ロックをかけたいので、再ロック処理をawaitで止めておく
                await this.InfOk().then((res)=>{
                    this.onEdit();
                });
            }
        },

        InfOk() {
            return Swal.fire({
                title: '注意',
                html: '保存してから画面を離れてください',
                icon: 'info',
                showCancelButton: false,
                confirmButtonText: 'OK'
            })
        },
    }
};
</script>
<style src="@/_CommonCss/Common.css"></style>
<style src="@/_CommonCss/Base.css"></style>