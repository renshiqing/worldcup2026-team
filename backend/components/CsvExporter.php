<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：backend/components/CsvExporter.php
 * 功能：CSV 导出工具（UTF-8 BOM + 公式注入防护）
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace backend\components;

use Yii;

/**
 * 后台列表导出 CSV。
 */
class CsvExporter
{
    /**
     * 把二维数组输出为 CSV 下载。
     *
     * @param array $rows 第一行是表头
     * @param string $filename 下载文件名
     * @return \yii\web\Response
     */
    public static function send(array $rows, $filename)
    {
        $handle = fopen('php://temp', 'r+');

        // UTF-8 BOM，Excel 打开中文不乱码。
        fwrite($handle, "\xEF\xBB\xBF");

        foreach ($rows as $row) {
            fputcsv($handle, array_map([self::class, 'cell'], $row));
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Yii::$app->response->sendContentAsFile($content, $filename, [
            'mimeType' => 'text/csv',
        ]);
    }

    /**
     * 防止 CSV 公式注入（Excel 会把 = + - @ 开头的单元格当公式）。
     *
     * @param mixed $value
     * @return string
     */
    public static function cell($value)
    {
        $value = (string) $value;

        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@'], true)) {
            return "'" . $value;
        }

        return $value;
    }
}
