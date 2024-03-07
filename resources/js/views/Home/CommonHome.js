export const statusList = [
    { value: 0, text: "構成着手中" },
    { value: 1, text: "構成薬機確認中" },
    { value: 2, text: "構成薬機確認済（LP着手中）" },
    { value: 3, text: "LP薬機確認中" },
    { value: 4, text: "LP薬機確認済" },
    { value: 5, text: "完了" }
];

/**
 * ステータス名を返す
 * @param {*} statusId
 * @returns
 */
export function getStatusName(statusId) {
    const hit = statusList.find(item => item.value == statusId);
    return hit.text;
}