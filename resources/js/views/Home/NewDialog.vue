<template>
    <v-dialog max-width="500" scrollable v-model="dialog">
        <v-card class="dialog_box">
            <v-card-title class="card_title">
                <v-icon size="30px" class="pr-4">fa-solid fa-pen-to-square</v-icon>
                <span v-if="model.id == null" style="width:85%;">新規作成</span>
                <span v-else style="width:85%;">構成情報の編集</span>
                <v-icon @click="dialog = false" medium>fa-times</v-icon>
            </v-card-title>
            <v-card-text>
                <v-form v-model="valid">
                    <v-text-field label="商品名" v-model="model.product_name" outlined clearable dense></v-text-field>
                    <v-text-field
                        label="コード（サプリコード/品目コード 等）"
                        v-model="model.product_code"
                        outlined
                        clearable
                        dense
                        type="tel"
                        :rules="[rules.half]"
                    ></v-text-field>
                    <v-text-field
                        label="簡易説明（薬機修正/楽天用リニュ/新規LP作成 等）"
                        v-model="model.description"
                        outlined
                        clearable
                        dense
                    ></v-text-field>
                    <div class="hokan_check_box">
                        <div class="hokan_art">構成案の保管（チェックの付いていない構成は2年後に自動で削除されます）</div>
                        <v-checkbox v-model="model.requirement_flag" label="構成案を保管する（サプリ構成は保管必須）"></v-checkbox>
                    </div>
                    <v-select
                        v-model="model.status"
                        label="進捗"
                        :items="statusList"
                        item-value="value"
                        item-text="text"
                        dense
                        outlined
                        hide-details
                        class="mb-4"
                    ></v-select>
                    <div class="select_btn_box">
                        <div class="dialog_item_edit_btn">
                            <v-btn @click="save" :disabled="!valid">{{newFlag ? '登録':'更新'}}</v-btn>
                        </div>
                        <div v-if="!newFlag" class="dialog_item_clear_btn">
                            <v-btn class="cancel_btn_color" @click="dialog = false">キャンセル</v-btn>
                        </div>
                    </div>
                </v-form>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import { statusList } from './CommonHome.js';
export default {
    props: {
        value: {
            type: Boolean,
            default: false
        },

        model: {
            type: Object,
            default: () => {}
        }
    },

    data() {
        return {
            statusList,
            rules:{
                half:(v) => v.match(/^[A-Za-z0-9]*$/) || '半角英数字で入力してください'
            },
            valid:false
        }
    },

    computed: {
        dialog: {
            get() {
                return this.value;
            },
            set(newVal) {
                this.$emit("input", newVal);
            }
        },

        // 登録or更新の制御
        newFlag() {
            return `id` in this.model ? false : true;
        }
    },

    methods: {
        // 保存
        save: async function() {
            // 振り分け
            if (this.newFlag) {
                this.create();
                return;
            }
            this.update();
        },

        // 登録
        create: async function() {
            const params = this.model;
            const result = await this.$axios({
                method: "POST",
                url: "/home",
                data: params
            });
            this.dialog = false;
            this.$emit("close");
        },

        // 更新
        update: async function() {
            const params = this.model;
            const result = await this.$axios({
                method: "PUT",
                url: "/home/" + this.model.id,
                data: params
            });
            this.dialog = false;
            this.$emit("close");
        },
    }
};
</script>

<style>
</style>