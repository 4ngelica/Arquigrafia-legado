<?php namespace lib\draft;

trait DraftingTrait {
  
  public static function bootDraftingTrait() {
    static::addGlobalScope(new DraftingScope);
  }

  public static function onlyDrafts()
  {
    $instance = new static;

    $column = $instance->getQualifiedDraftColumn();

    return $instance->newQueryWithoutScope(new DraftingScope)->whereNotNull($column);
  }

  public function getQualifiedDraftColumn() {
    return 'photos.draft';
  }

}