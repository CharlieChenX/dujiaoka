
<div class="form-group">
    <label class="control-label">服务器IP：</label>
    <input type="text" name="hostname" class="form-control" required lay-verify="required" placeholder="请输入服务器的IP地址">
</div>

<div class="form-group">
    <label class="control-label">SSH用户名：</label>
    <input type="text" name="sshuser" class="form-control" required lay-verify="required" value='root' readonly>
</div>

<div class="form-group" style="width: 70%; display: inline-block;">
    <label class="control-label">SSH密码/密钥：</label>
    <input type="password" name="sshverify" class="form-control" required lay-verify="required" placeholder="请输入服务器SSH登陆的密码或密钥">
</div>
<div class="form-group" style="width: 28%; display: inline-block;">
    <select class="form-control" name="sshmethod" required lay-verify="required">
        <option value="password">密码</option>
        <option value="key">密钥</option>
    </select>
</div>

<div class="form-group">
    <label class="control-label">网站域名：</label>
    <input type="text" name="domain" class="form-control" placeholder="例如:www.heli8.top">
</div>
