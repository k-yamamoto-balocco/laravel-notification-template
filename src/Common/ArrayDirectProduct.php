<?php

namespace GitBalocco\LaravelNotificationTemplate\Common;

use IteratorAggregate;
use Traversable;

/**
 * Class ArrayDirectProduct
 * 配列と配列の直積を扱うためのクラス
 * 要素群1 (1,2) と要素群2 (a,b) の組み合わせ4パターンを扱いたいときなどに利用する。
 * 二重、三重のforeach文を書くことを回避する目的で作成。
 * @package GitBalocco\LaravelNotificationTemplate\Common
 *
 * ソースコード自体は下記をそのままパクっている・・・
 * @see https://qiita.com/mpyw/items/e0b6e0842a460b3f901f
 */
class ArrayDirectProduct implements IteratorAggregate
{
    /** @var array[] */
    private $arrays;

    /**
     * ArrayDirectProduct constructor.
     * @param array ...$arrays
     */
    public function __construct(array ...$arrays)
    {
        $this->arrays = $arrays;
    }

    /**
     * @return Traversable
     */
    public function getIterator()
    {
        return $this->exec(...$this->arrays);
    }

    /**
     * @param array ...$arrays
     * @return Traversable
     */
    protected function exec(array ...$arrays): Traversable
    {
        if (!$arrays) {
            yield [];
        } elseif ($tails = array_pop($arrays)) {
            foreach ($this->exec(...$arrays) as $body) {
                foreach ($tails as $tail) {
                    yield array_merge($body, [$tail]);
                }
            }
        }
    }
}
