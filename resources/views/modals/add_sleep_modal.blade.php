<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="addSleepModalLabel">Sleeping</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="meal-time-input" class="sr-only">Time</label>
                <div class="input-group clockpicker" data-placement="right" data-align="top"
                     data-autoclose="true">
                    <input id="sleep-time-input" type="text" class="form-control"
                           value="{{ Carbon::now()->format('H:i') }}" readonly="true">
                    <span class="input-group-addon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save</button>
        </div>
    </div>
</div>
