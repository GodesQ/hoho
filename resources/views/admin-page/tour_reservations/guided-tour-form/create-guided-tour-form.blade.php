<div id="guided-tour-form" style="width: 100%; height: 100%;">
    <div class="row">
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="tour" class="form-label">Tour</label>
                <select name="tour_id" id="tour" class="select2 form-select">
                    @foreach ($tours as $guided_tour)
                        <option value="{{ $guided_tour->id }}">{{ $guided_tour->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="user" class="form-label">Reserved User</label>
                <select name="resevered_user_id" id="user" class="reserved_users form-select" style="width: 100%;">
                </select>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="number_of_pass" class="form-label"></label>
                <select name="number_of_pass" id="number_of_pass" class="form-select">
                    <option value=""></option>
                </select>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <label class="form-label">Registered Passengers</label>
                <select name="passenger_ids" id="passengers" class="registered_passengers form-select" style="width: 100%;" multiple></select>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>

    </script>
@endpush
