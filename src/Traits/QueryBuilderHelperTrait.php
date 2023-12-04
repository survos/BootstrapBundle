<?php


namespace Survos\BootstrapBundle\Traits;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @deprecated "Use core-bundle"
 */
trait QueryBuilderHelperTrait
{
    public function getCounts($field): array
    {
        assert(false, "deprecated, use core-bundle");

        $results = $this->createQueryBuilder('s')
            ->groupBy('s.' . $field)
            ->select(["s.$field, count(s) as count"])
            ->getQuery()
            ->getArrayResult();
        $counts = [];
        foreach ($results as $r) {
            $counts[$r[$field]] = $r['count'];
        }

        return $counts;
    }

    public function findBygetCountsByField($field = 'marking', $filters = [], ?string $idField = 'id'): array
    {
        assert(false, "deprecated, use core-bundle");
        $filters = (new OptionsResolver())
            ->setDefaults([
                'media' => null,
            ])->resolve($filters);

        $qb = $this->createQueryBuilder('article')
            // ->where("h.currentState = 'new'")
            ->select(sprintf('COUNT(article%s) as c, article.%s as field ', $idField ? '.' . $idField : '', $field));

        foreach ($filters as $table => $value) {
            if ($value = $filters[$table]) {
                $qb->join('article.' . $table, $table);
                if (true || $value) {
                    $qb->andWhere("article.$table = :$table")->setParameter($table, $value);
                }
            }
        }

        $counts = [];
        $markingCounts = $qb
            ->groupBy('article.' . $field)
            ->getQuery()
            ->getResult();

        foreach ($markingCounts as $x) {
            $counts[$x['field']] = $x['c'];
        }
        return $counts;
    }
}
