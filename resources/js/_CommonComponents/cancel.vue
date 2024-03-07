<template>
    <v-dialog max-width="500" scrollable v-model="dialog">
        <v-card class="dialog_box">
            <v-card-title class="card_title">
                <span style="width:85%;">キャンセル</span>
                <v-icon @click="dialog = false" medium style="margin-left: auto;">fa-times</v-icon>
            </v-card-title>
            <v-card-text>
                <span class="alert_messege mb-4">行った変更が保存されません。よろしいですか？</span>
                <div class="select_btn_box">
                    <div class="dialog_item_edit_btn">
                        <v-btn @click="onCancel">はい</v-btn>
                    </div>
                    <div class="dialog_item_clear_btn">
                        <v-btn class="cancel_btn_color" @click="dialog = false">キャンセル</v-btn>
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    data(){
        return {
            dialog:false,
        }
    },
    methods: {
        open(){
            this.dialog = true
        },
        onCancel: async function() {
            this.dialog = false;
            this.$emit("calncel_action");
        }
    }
};
</script>

<style>
</style>