<h3>家长感悟</h3>
<form action="/fbt/commentmodifyPost" method="post">
    <input type="hidden" name="commentid" value="<?= $comment->id ?>"/>
    <div class="table-responsive">
        <table class="table table-bordered">
        <tr>
            <th width=60>评论ID</th>
            <td><?= $comment->id ?></td>
        </tr>
        <tr>
            <th>时间</th>
            <td><?= $comment->createtime ?></td>
        </tr>
        <tr>
            <th>标题</th>
            <td>
                <input type="text" name="title" style="width: 80%" value="<?= $comment->title ?>"/>
            </td>
        </tr>
        <tr>
            <th>内容</th>
            <td>
                <textarea id="content" name="content" cols=60 rows=10><?= $comment->content ?></textarea>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <input type="submit" class="btn-save" value="保存修改"/>
            </td>
        </tr>
    </table>
    </div>
</form>
