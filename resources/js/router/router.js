
import Router from 'vue-router';
import Home from '@/views/Home/Home.vue';
import LevelSelect from '@/views/LevelSelect/LevelSelect.vue';
import ItemKarte from '@/views/ItemKarte/ItemKarte.vue';
import BasicKnowledge from '@/views/BasicKnowledge/Index.vue';
import ComparisonInsert from '@/views/ComparisonInsert/Index.vue';
import CompaniesComparison from '@/views/CompaniesComparison/Index.vue';
import ConstitutionProcess from '@/views/ConstitutionProcess/ConstitutionProcess.vue';
import ConstitutionPlan from '@/views/ConstitutionPlan/ConstitutionPlan.vue';
import FinalDesignConfirmation from '@/views/FinalDesignConfirmation/FinalDesignConfirmation.vue';
import store from '@/store/index.js';

const router = new Router({
    mode: 'history',
    base: '/lp-easy-order/',
    routes: [
        /*******************コンポーネントリンク**********************/
        {
            path: '/home',
            name: 'Home',
            component: Home
        },
        {
            path: '/level_select/:id',
            name: 'LevelSelect',
            component: LevelSelect
        },
        {
            path: '/item_karte/:id',
            name: 'ItemKarte',
            component: ItemKarte
        },
        {
            path: '/basic_knowledge/:id',
            name: 'BasicKnowledge',
            component: BasicKnowledge
        },
        {
            path: '/comparison_insert/:id',
            name: 'ComparisonInsert',
            component: ComparisonInsert
        },
        {
            path: '/companies_comparison/:id',
            name: 'CompaniesComparison',
            component: CompaniesComparison
        },
        {
            path: '/constitution_process/:id',
            name: 'ConstitutionProcess',
            component: ConstitutionProcess
        },
        {
            path: '/constitution_plan/:id',
            name: 'ConstitutionPlan',
            component: ConstitutionPlan
        },
        {
            path: '/final_design_confirmation/:id',
            name: 'FinalDesignConfirmation',
            component: FinalDesignConfirmation
        },
    ]
});

// 画面遷移前にCALLされる関数
router.beforeEach((to, from, next) => {
    store.commit('common/setBeforeRouting', from.path);
    next();
});

export default router;