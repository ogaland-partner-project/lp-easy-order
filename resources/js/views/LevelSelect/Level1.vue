<template>
    <div>
        <div class="level-one-aria">
            <v-textarea
                class="outlined_field"
                label="どういう売り方？訴求？・目的"
                v-model="levelModel.purpose"
                outlined
                auto-grow
            ></v-textarea>
            <div class="level-aria_info">
                新商品の場合は商品カルテ欄の書けるところをご記入ください<br>
                リニューアルの場合は変えたい理由を簡単にご記入ください
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