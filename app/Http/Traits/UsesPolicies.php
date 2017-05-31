<?php

/** Created by PhpStorm,  User: jonphipps,  Date: 2017-05-30,  Time: 11:57 AM */

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait UsesPolicies
{
    public function create()
    {
        $this->policyAuthorize('create', $this->crud->getModel());

        return parent::create();
    }

    public function destroy($id)
    {
        $this->policyAuthorize('delete', $this->crud->getModel(), $id);

        return parent::destroy($id);
    }

    public function edit($id)
    {
        $this->policyAuthorize('update', $this->crud->getModel(), $id);

        return parent::edit($id);
    }

    public function index()
    {
        $this->policyAuthorize('index', $this->crud->getModel());

        return parent::index();
    }

    public function show($id)
    {
        $this->policyAuthorize('show', $this->crud->getModel(), $id);

        return parent::show($id);
    }

    /**
     * Determines the access to allow based on policy
     *
     * @param string   $ability The ability to validate
     * @param Model    $class   The instance of a Model class to check against
     * @param int|null $id      The id of the individual to check against
     */
    protected function policyAuthorize($ability, $class, $id = null): void
    {
        $model = $id !== null ? $class::findOrFail($id) : $class;

        if (Auth::check() && Auth::user()->can($ability, $model)) {
            $this->crud->allowAccess([ $ability ]);
        }
    }
}
