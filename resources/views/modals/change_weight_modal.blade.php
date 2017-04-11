<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="changeWeightModalLabel">Edit Weight</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="weight-input" class="sr-only">Weight in kg</label>
                <div class="input-group">
                    <input class="form-control focus" type="number" id="weight-input"
                           placeholder="Weight in kg">
                    <div class="input-group-addon">kg</div>
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--<label for="weight-input" class="sr-only">Height in cm</label>--}}
                {{--<div class="input-group">--}}
                    {{--<input class="form-control focus" type="number" id="height-input"--}}
                           {{--placeholder="Height in cm">--}}
                    {{--<div class="input-group-addon">cm</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save</button>
        </div>
    </div>
</div>
