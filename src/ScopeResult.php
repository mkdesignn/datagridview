<?php

namespace mkdesignn\datagridview;


trait ScopeResult
{
    public function scopeResult($query){
        return [$query->toSql(), $query->getBindings()];
    }
}