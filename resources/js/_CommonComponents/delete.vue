<template>
    <v-dialog max-width="500" scrollable v-model="dialog">
        <v-card class="dialog_box">
            <v-card-title class="card_title">
                <v-icon size="30px" class="pr-4">fa-solid fa-trash-can</v-icon>
                <span style="width:85%;">削除確認</span>
                <v-icon @click="dialog=false" medium>fa-times</v-icon>
            </v-card-title>
            <v-card-text>
                <span class="alert_messege mb-4">本当に削除しますか？</span>
                <div class="select_btn_box">
                    <div class="dialog_item_edit_btn">
                        <v-btn class="delete_btn_color" @click="clear_action">削除</v-btn>
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
    methods:{
        open(){
            this.dialog = true
        },
        clear_action(){
            this.$emit('delete_action');
            this.dialog = false;
        }
    }
}
</script>

<style>
</style>
