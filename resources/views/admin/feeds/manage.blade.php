@extends(backpack_view('layouts.top_left'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs" id="feedManagerTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="config-tab" data-toggle="tab" href="#config" role="tab" aria-controls="config" aria-selected="true">Configuration</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="filtering-tab" data-toggle="tab" href="#filtering" role="tab" aria-controls="filtering" aria-selected="false">Filtering Rules</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="mapping-tab" data-toggle="tab" href="#mapping" role="tab" aria-controls="mapping" aria-selected="false">Field Mapping</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="transformation-tab" data-toggle="tab" href="#transformation" role="tab" aria-controls="transformation" aria-selected="false">Transformation Rules</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="destinations-tab" data-toggle="tab" href="#destinations" role="tab" aria-controls="destinations" aria-selected="false">Destinations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">Import History</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="feedManagerTabsContent">
                        <div class="tab-pane fade show active" id="config" role="tabpanel" aria-labelledby="config-tab">
                            <h3>Feed Configuration</h3>
                            <p>This is where the basic feed configuration form will go.</p>
                            </div>
                        <div class="tab-pane fade" id="filtering" role="tabpanel" aria-labelledby="filtering-tab">
                            <h3>Product Filtering Rules</h3>
                            <p>This is where we'll manage which products enter the pipeline.</p>
                            </div>
                        <div class="tab-pane fade" id="mapping" role="tabpanel" aria-labelledby="mapping-tab">
                            <h3>Field Mapping</h3>
                            <p>This is where we'll map source fields to destination fields.</p>
                            </div>
                        <div class="tab-pane fade" id="transformation" role="tabpanel" aria-labelledby="transformation-tab">
                            <h3>Advanced Transformation Rules</h3>
                            <p>This is where the IF/THEN rules for this feed will be managed.</p>
                             </div>
                        <div class="tab-pane fade" id="destinations" role="tabpanel" aria-labelledby="destinations-tab">
                            <h3>Syndication Destinations</h3>
                             <p>This is where we will manage which websites receive data from this feed.</p>
                             </div>
                         <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                            <h3>Import History</h3>
                             <p>This is where a list of recent Import Jobs for this feed will be shown.</p>
                             </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection