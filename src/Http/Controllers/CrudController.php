<?php

namespace berthott\Crudable\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use berthott\Crudable\Http\Requests\UpdateRequest;
use berthott\Crudable\Models\Contracts\Targetable;
use berthott\Crudable\Models\Traits\Targetable as TraitsTargetable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CrudController implements Targetable
{
  use TraitsTargetable, AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  /**
   * Display a listing of the resource.
   *
   * @return Collection
   */
  public function index(): Collection
  {
      return $this->target::all();
  }

  /**
   * Display the specified resource.
   *
   * @param  mixed  $id
   * @return Model
   */
  public function show($id): Model
  {
      return $this->target::findOrFail($id);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  UpdateRequest  $request
   * @return Model
   */
  public function store(UpdateRequest $request): Model
  {
      return $this->target::create($request->validated());
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  mixed $id
   * @return Model
   */
  public function update(UpdateRequest $request, $id): Model
  {
      $instance = $this->target::findOrFail($id);
      $instance->update($request->validated());
      return $instance;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  mixed $id
   * @return Response
   */
  public function destroy($id)
  {
      return $this->target::destroy($id);
  }
}