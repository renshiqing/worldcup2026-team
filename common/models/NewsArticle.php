<?php
/**
 * 世界杯赛事信息与球迷交流平台
 * 文件：common/models/NewsArticle.php
 * 功能：新闻模型（news_article），草稿/发布状态
 * 作者：C（后台管理与统计）
 * 日期：2026-11-01
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * 新闻文章。
 *
 * 对应数据表 news_article。author_id 由 C 的迁移 m261101_120000 新增，
 * 用于记录是哪位管理员发布的新闻。
 *
 * @property int $id
 * @property string $title
 * @property string|null $summary
 * @property string $content
 * @property string $category
 * @property string $status
 * @property string|null $published_at
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $author_id
 *
 * @property User $author
 */
class NewsArticle extends ActiveRecord
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%news_article}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['summary'], 'string'],
            [['title'], 'string', 'max' => 200],
            [['category'], 'string', 'max' => 50],
            [['category'], 'default', 'value' => '赛事新闻'],
            [['status'], 'default', 'value' => self::STATUS_DRAFT],
            [['status'], 'in', 'range' => array_keys(self::statuses())],
            [['published_at'], 'safe'],
            [['author_id', 'created_at', 'updated_at'], 'integer'],
            [
                ['author_id'],
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => ['author_id' => 'id'],
                'skipOnEmpty' => true,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '新闻编号',
            'title' => '标题',
            'summary' => '摘要',
            'content' => '正文',
            'category' => '分类',
            'status' => '状态',
            'published_at' => '发布时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'author_id' => '发布人',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $now = time();
        $this->updated_at = $now;

        if ($insert) {
            $this->created_at = $now;
        }

        if ($this->status === self::STATUS_PUBLISHED) {
            if (empty($this->published_at)) {
                $this->published_at = date('Y-m-d H:i:s', $now);
            }
        } else {
            // 撤回为草稿后不再保留发布时间，前台按 status 过滤即可。
            $this->published_at = null;
        }

        return true;
    }

    /**
     * 状态选项。
     *
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => '草稿',
            self::STATUS_PUBLISHED => '已发布',
        ];
    }

    /**
     * 分类选项。
     *
     * @return array
     */
    public static function categories()
    {
        return [
            '赛事新闻' => '赛事新闻',
            '球队动态' => '球队动态',
            '公告' => '公告',
        ];
    }

    /**
     * 当前状态的中文名称。
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $statuses = self::statuses();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : $this->status;
    }

    /**
     * 发布人。
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * 只包含已发布新闻的查询。
     *
     * @return \yii\db\ActiveQuery
     */
    public static function findPublished()
    {
        return static::find()->where(['status' => self::STATUS_PUBLISHED]);
    }

    /**
     * 前台或后台展示用的发布时间。
     *
     * @return string
     */
    public function getPublishedAtText()
    {
        return empty($this->published_at) ? '—' : $this->published_at;
    }
}
