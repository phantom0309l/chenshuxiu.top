  <div style="margin-top:20px;margin-bottom:10px;" class="title">
      <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
      <span>不服药记录</span>
  </div>

  <table class="table table-bordered" style="text-align:center;">
      <thead>
          <tr>
              <th>更新日期</th>
              <th>用药状态</th>
              <th>运营</th>
              <th>操作</th>
          </tr>
      </thead>
      <tbody>
  <?php foreach ($drugsheets as $a) {
      ?>
          <tr>
              <td><?= $a->thedate ?></td>
              <td>
                  <?= 1 == $a->is_nodrug ? "不服药" : "" ?>
              </td>
              <td>
                  <?= $a->auditor->name ?>
              </td>
              <td>
                  <button class="deleteDrugSheetBtn btn btn-default" data-drugsheetid="<?= $a->id ?>">删除</button>
              </td>
          </tr>
  <?php } ?>
      </tbody>
  </table>
