
테스트 메일

<form action='test_mail.php' method="POST" name='frm' enctype="multipart/form-data">
    <table>
        <tr>
            <td>받는사람<input type='text' name='to_email'></td>
        </tr>
        <tr>
            <td>보내는사람이메일<input type='text' name='from_email'></td>
        </tr>
        <tr>
            <td>보내는사람이름<input type='text' name='from_name'></td>
        </tr>
        <tr>
            <td>제목<input type='text' name='subject'></td>
        </tr>
        <tr>
            <td>내용<input type='text' name='body'></td>
        </tr>
        <tr>
            <td>첨부<input type='file' name='file'></td>
        </tr>
        <tr>
            <td><input type='submit' value='이메일보내기TEST'>
            </td>
        </tr>
</form>
