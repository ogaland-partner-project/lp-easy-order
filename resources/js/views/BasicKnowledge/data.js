/**
 * 画面表示用モデルの初期状態
 * 項目追加時に使用
 */
export const initKnowledgeModel = {
    "basic_knowledge_id":null,
    "question":null,
    "others":null,
    "details":[],
    "images":[],
    "urls":[]
}

/**
 * 必要最低限の知識入力項目
 * 項目追加時に使用
 */
export const initKnowledgeItems = [
    {
        title: "商品/原材料名",
        detail: "",
        sort_order: 1,
    },
    {
        title: "効果効能",
        detail: "",
        sort_order: 2,
    },
    {
        title: "特徴",
        detail: "",
        sort_order: 3,
    },
    {
        title: "使用感/\n試食感想 等",
        detail: "",
        sort_order: 4,
    },
    {
        title: "疑問点",
        detail: "",
        sort_order: 5,
    },
    {
        title: "他社の安心ポイント\n(証明書など)",
        detail: "",
        sort_order: 3,
    },
]

/**
 * URLリストの要素を定義
 * - 初期値に使用
 */
export const initUrlModel = {
    url: ""
}