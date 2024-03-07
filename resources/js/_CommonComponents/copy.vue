<template>
    <v-dialog max-width="500" scrollable v-model="dialog">
        <v-card>
            <v-card-title>
                <v-icon size="30px" class="pr-4">mdi-content-copy</v-icon>
                <span style="width:85%;">コピー</span>
                <v-icon @click="dialog=false" medium>fa-times</v-icon>
            </v-card-title>
            <v-card-text>
                <v-autocomplete
                    label="コピー元を選択"
                    outlined
                    :items="order"
                    item-text="goods"
                    item-value="id"
                    v-model="selectItem"
                >
                <template slot="item" slot-scope="{ item }">
                    <span>{{item.id}} : {{item.goods}}</span>
                </template>
                </v-autocomplete>
                <v-btn class="mb-3" @click="copy_action">コピーする</v-btn>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    data(){
        return {
            dialog:false,
            order:[
                {id:1,goods:'ミックスナッツ',instructions:'ミックスナッツ説明',copy:'コピー'},
                {id:2,goods:'マザーズバッグ',instructions:'マザーズバッグ説明',copy:'コピー'},
                {id:3,goods:'おしりふき',instructions:'おしりふき説明',copy:'コピー'},
                {id:4,goods:'つまみまくるみ',instructions:'つまみまくるみ説明',copy:'コピー'},
                {id:5,goods:'ほしいも',instructions:'ほしいも説明',copy:'コピー'},
            ],
            selectItem:1,
        }
    },
    methods:{
        open(){
            this.dialog = true
        },
        copy_action(){
            this.$emit('copy_action',this.selectItem);
            this.dialog = false;
        }
    }
}
</script>

<style>

</style>