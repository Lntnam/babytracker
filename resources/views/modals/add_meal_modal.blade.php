<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="addMealModalLabel">Add Meal</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="meal-time-input" class="sr-only">Time</label>
                <div class="input-group clockpicker" data-placement="right" data-align="top"
                     data-autoclose="true">
                    <input id="meal-time-input" type="text" class="form-control"
                           value="{{ Carbon::now()->format('H:i') }}" readonly="true">
                    <span class="input-group-addon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                </div>
            </div>
            <div class="form-group">
                <label for="meal-input" class="sr-only">ml</label>
                <div class="input-group">
                    <input class="form-control" type="number" id="meal-input" placeholder="Amount in ml">
                    <div class="input-group-addon">ml</div>
                </div>
            </div>
            <div class="form-group">
                <label class="sr-only">Feeding Type</label>
                <label class="custom-control custom-radio">
                    <input id="bottle-fed" name="feed_type" value="bottle" type="radio"
                           class="custom-control-input" checked="checked">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Bottle</span>
                </label>
                <label class="custom-control custom-radio">
                    <input id="breast-fed" name="feed_type" value="breast" type="radio"
                           class="custom-control-input">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Breast</span>
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save</button>
        </div>
    </div>
</div>
