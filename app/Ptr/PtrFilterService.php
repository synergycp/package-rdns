<?php

namespace Packages\Rdns\App\Ptr;

use App\Entity\EntityFilterService;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Http\FilterService;

/**
 * Filter Ptrs by those visible to the specific Request.
 */
class PtrFilterService extends FilterService {
  /**
   * @var PtrListRequest
   */
  protected $request;
  protected $requestClass = PtrListRequest::class;

  /**
   * @var PtrRepository
   */
  private $ptrs;

  /**
   * @var EntityFilterService
   */
  private $entityFilter;

  /**
   * PtrFilterService constructor.
   *
   * @param PtrRepository       $ptrs
   * @param EntityFilterService $entityFilter
   */
  public function boot(PtrRepository $ptrs, EntityFilterService $entityFilter) {
    $this->ptrs = $ptrs;
    $this->entityFilter = $entityFilter;
  }

  /**
   * @param Builder $query
   */
  public function viewable(Builder $query) {
    $this->auth->only([
      'admin',
      'integration',
      'client' => function () use ($query) {
        $query->whereNotNull('entity_id');
        $this->ptrs->joinEntity($query);
        $this->entityFilter->viewable($query);

        $query
          ->select($this->ptrs->getTable() . ".*")
          ->groupBy($this->ptrs->getTable() . ".id");
      },
    ]);

    return $query;
  }

  public function query(Builder $query) {
    $this->prepare()->apply($query);

    // Filter raw text search
    if ($searchText = $this->request->input('q')) {
      $query->search($this->search->search($searchText));
    }

    if ($entity = $this->request->input('entity')) {
      $query->whereIn('entity_id', array_flatten([$entity]));
    }

    return $query;
  }
}
