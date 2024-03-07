<template>
    <div>
        <div class="level-four-aria">
            <v-textarea
                class="outlined_field"
                label="どういう売り方？訴求？・目的"
                v-model="levelModel.purpose"
                outlined
                auto-grow
            ></v-textarea>
            <v-textarea
                class="outlined_field"
                label="デザイン・テイスト"
                v-model="levelModel.taste"
                outlined
                auto-grow
            ></v-textarea>
            <div class="level-aria_info">
                構成案シートをすべて埋め、薬機修正後にご依頼ください。
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        // 親から元データ引継ぎ
        levelModel: {
            type: Object,
            'default': () => ({})
        }
    },
}
</script>