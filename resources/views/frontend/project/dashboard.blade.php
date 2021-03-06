@extends('backpack::layout')
<?php
/** @var \App\Models\Project $project */
/** @var \App\Http\Controllers\Frontend\Vocabulary\VocabularyCrudController $vocabulary */
/** @var \App\Models\Import $import */
/** @var \App\Models\Batch $batch */
$showReleaseButton = $showImportButton = (count($project->vocabularies) || count($project->elementsets));
?>
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1>{{ $project->title }}</h1>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6" id="vocabularies">
                            <div class="panel panel-default">
                                @include('frontend.partials.panelheader', ['crud' => $vocabulary->crud, 'policy_model' => $project, 'permission' =>'edit' ])<!--panel-heading-->
                                <div class="panel-body">
                                    <ul class="list-unstyled">
                                        @forelse ($project->vocabularies->sortBy('name') as $vocab)
                                            <li>{{ laravel_link_to('vocabularies/'.$vocab->id . '/concepts', $vocab->name) }}</li>
                                        @empty
                                            No Vocabularies defined
                                        @endforelse
                                    </ul>
                                </div><!--panel-body-->
                            </div><!--panel-->
                        </div><!--col-md-6-->
                        <div class="col-md-6" id="elementsets">
                            <div class="panel panel-default">
                            @include('frontend.partials.panelheader', ['crud' => $elementset->crud, 'policy_model' => $project, 'permission' =>'edit' ])<!--panel-heading-->
                                <div class="panel-body">
                                    <ul class="list-unstyled">
                                        @forelse ($project->elementsets->sortBy('name') as $elementset)
                                            <li>{{ laravel_link_to('elementsets/'.$elementset->id . '/elements', $elementset->name) }}</li>
                                        @empty
                                            No Element Sets defined
                                        @endforelse
                                    </ul>
                                </div><!--panel-body-->
                            </div><!--panel-->
                        </div><!--col-md-6-->
                    </div><!--row-->
                    <div class="row">
                        <div class="col-md-6" id="detail">
                            <div class="panel panel-default">
                                <div class="panel-heading clearfix">
                                    <h3 class="pull-left" style="margin-top: 8px; margin-bottom: 6px">Project Detail</h3>
                                </div><!--panel-heading-->
                                <div class="panel-body">
                                    <table class="table table-bordered table-condensed">
                                        <tbody>
                                            <tr class="warning">
                                                <td colspan="2"><h4>Metadata</h4></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Created At</strong></td>
                                                <td>{{$project->created_at->toFormattedDateString()}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Updated At</strong></td>
                                                <td>{{$project->updated_at->toFormattedDateString()}}</td>
                                            </tr>
                                            <tr class="warning">
                                                <td colspan="2"><h4>Description</h4></td>
                                            </tr>
                                        @foreach ($crud->columns as $column)
                                            <tr>
                                                <td>
                                                    <strong>{{ $column['label'] }}</strong>
                                                </td>
                                                @if (!isset($column['type']))
                                                    @include('crud::columns.text')
                                                @else
                                                    @if(view()->exists('vendor.backpack.crud.columns.'.$column['type']))
                                                        @include('vendor.backpack.crud.columns.'.$column['type'])
                                                    @else
                                                        @if(view()->exists('crud::columns.'.$column['type']))
                                                            @include('crud::columns.'.$column['type'])
                                                        @else
                                                            @include('crud::columns.text')
                                                        @endif
                                                    @endif
                                                @endif
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @can('edit', $project)
                                        <a class="btn btn-default btn-sm pull-right" href="{{route('frontend.crud.projects.edit', ['id' => $project->id])}}">Edit</a>
                                    @endcan
                                </div><!--panel-body-->
                            </div><!--panel-->
                        </div><!--col-xs-12-->
                        <div class="col-md-6" id="releases">
                            <div class="panel panel-default">
                            @include('frontend.partials.panelheader', ['crud' => $release->crud, 'policy_model' => $project, 'permission' =>'publish', $showButton = $showReleaseButton ])<!--panel-heading-->
                                <div class="panel-body">
                                    <ul class="list-unstyled">
                                        @forelse ($project->releases->sortBy('name') as $release)
                                            <li>{{ laravel_link_to("projects/{$project->id}/releases/$release->id", $release->name) }}</li>
                                        @empty
                                            This project has no releases
                                        @endforelse
                                    </ul>
                                </div><!--panel-body-->
                            </div><!--panel-->
                        </div><!--col-md-6-->
                    </div><!--row-->
                    <div class="row">
                        <div class="col-md-6" id="exports">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3>Exports</h3>
                                </div><!--panel-heading-->
                                <div class="panel-body">
                                    <p>This will be a list of Project-level Exports
                                        (maybe)</p>
                                </div><!--panel-body-->
                            </div><!--panel-->
                        </div><!--col-md-6-->
                        <div class="col-md-6" id="imports">
                            <div class="panel panel-default">
                            @include('frontend.partials.panelheader', ['crud' => $import->crud, 'policy_model' => $project, 'permission' =>'edit', $showButton = $showImportButton ])<!--panel-heading-->
                                <div class="panel-body">
                                    <table class="table table-bordered table-striped table-hover table-condensed table-responsive">
                                        <thead>
                                        <tr>
                                            <th>Import</th>
                                            <th>Started at</th>
                                            <th>Next step</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($project->importBatches->sortBy('created_at') as $batch)
                                            <tr>
                                                <td>
                                                    @can('edit', $project)
                                                        {{ laravel_link_to('projects/' . $project->id . '/imports/' . $batch->id . '/'. $batch->next_step , $batch->run_description) }}
                                                    @else
                                                        {{$batch->run_description}}
                                                    @endcan
                                                </td>
                                                <td>
                                                    {{ $batch->created_at }}
                                                </td>
                                                <td>
                                                    {{ laravel_link_to('projects/' . $project->id . '/imports/' . $batch->id . '/'. $batch->next_step , $batch->next_step) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Project Imports yet</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div><!--panel-body-->
                            </div><!--panel-->
                        </div><!--col-md-6-->
                    </div><!--row-->
                    <div class="row">
                        <div class="col-md-6" id="prefixes">
                            <div class="panel panel-default">
                            @include('frontend.partials.panelheader', ['crud' => $prefix->crud, 'policy_model' => $project, 'permission' =>'edit', $showButton = true ])<!--panel-heading-->
                                <div class="panel-body">
                                    <p>This will be a list of prefixes in use by this project</p>
                                </div><!--panel-body-->
                            </div><!--panel-->
                        </div><!--col-md-6-->
                        <div class="col-md-6" id="members">
                            <div class="panel panel-default">
                            @include('frontend.partials.panelheader', ['crud' => $member->crud, 'policy_model' => $project, 'permission' =>'edit', $showButton = true ])<!--panel-heading-->
                                <div class="panel-body">
                                    <table class="table table-bordered table-striped table-hover table-condensed table-responsive">
                                        <thead>
                                        <tr>
                                            <th>Nickname</th>
                                            <th>Languages</th>
                                            <th>Authorized As</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse (\App\Models\ProjectUser::with('user')->where('agent_id',$project->id)->get() as $projectUser)
                                            <tr>
                                                <td>
                                                    @if (isset($projectUser->user))
                                                        @can('edit', $project)
                                                            {{ laravel_link_to(url('projects/' . $project->id . '/members/' . $projectUser->id) , $projectUser->user->nickname) }}
                                                        @else
                                                            {{ $projectUser->user->nickname }}
                                                        @endif
                                                @endif
                                                </td>
                                                <td>
                                                    {{ $projectUser->showLanguagesCommaDelimited()}}
                                                </td>
                                                <td>
                                                    {{ $projectUser::GetAuthorizedAsString($projectUser->authorized_as)}}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Project Members yet (which is strange)</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div><!--panel-body-->
                            </div><!--panel-->
                        </div><!--col-md-6-->
                    </div><!--row-->
                    <div class="row">
                        <div class="col-md-6" id="maps">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3>Maps</h3>
                                </div><!--panel-heading-->
                                <div class="panel-body">
                                    <p>This will be a list of maps maintained by this project</p>
                                </div><!--panel-body-->
                            </div><!--panel-->
                        </div><!--col-md-6-->
                        <div class="col-md-6" id="profiles">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3>Profiles</h3>
                                </div><!--panel-heading-->
                                <div class="panel-body">
                                    <p>This will be a list of Application Profiles used by this project</p>
                                </div><!--panel-body-->
                            </div><!--panel-->
                        </div><!--col-md-6-->
                    </div><!--row-->
                </div><!--panel body-->
            </div><!-- panel -->
        </div><!-- col-md-10 -->
    </div><!-- row -->
@endsection
