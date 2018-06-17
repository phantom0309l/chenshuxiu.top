<div class="medicine_break_date-box" style="margin-bottom:10px; padding: 5px; border: 1px solid #ccc;">
    <lable>剩余药量可到</lable>
    <input type="text" class="calendar medicine_break_date" style="width: 100px" name="medicine_break_date" value="<?= '0000-00-00' != $patient->medicine_break_date ? $patient->medicine_break_date : '' ?>" />
    <button class="btn btn-primary medicine_break_date-save" data-patientid="<?= $patient->id ?>"> 保存 </button>
</div>
