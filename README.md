

# CRUD
## Deploy Steps
1. import `./init.php`
2. edit `./app/config.php` with database settings
3. set ***DOCUMENT_ROOT*** to `./public/index.php`
## Page
- ### /
    list all users
## API Interface
- ### `POST` /user
    create user

    Form data:

        {
            account: $account,
            name: $account,
            gender: $gender,
            birthday: $birthday,
            email: $email,
            note: $note,
        }
    - string ***`$account`***
    - string ***`$name`***
    - int ***`$gender`*** : 1 - male, 0 - female
    - date ***`$birthday`*** : Y-m-d
    - string ***`$email`***
    - string ***`$note`***
- ### `GET` /{id}
    view certain user info
    - int ***`id`***
- ### `PUT` /user/{id}
    update certain user info
    - int ***`id`***

    Form data:

        {
            account: $account,
            name: $account,
            gender: $gender,
            birthday: $birthday,
            email: $email,
            note: $note,
        }
    - string ***`$account`***
    - string ***`$name`***
    - int ***`$gender`*** : 1 - male, 0 - female
    - date ***`$birthday`*** : Y-m-d
    - string ***`$email`***
    - string ***`$note`***
- ### `DELETE` /user/{id}
    delete certain user
    - int ***`id`***

- #### Return Error Code
    <table>
        <thead>
            <tr>
                <th>errorCode</th>
                <th>type</th>
                <th>description</th>
            </tr>
        </thead>
        <tbody>
            <tr valign="top">
                <td>-1</td>
                <td>unknown</td>
                <td>不明狀態</td>
            </tr>
            <tr valign="top">
                <td>0</td>
                <td>success</td>
                <td>執行成功</td>
            </tr>
            <tr valign="top">
                <td>1</td>
                <td>fail</td>
                <td>執行失敗</td>
            </tr>
            <tr valign="top">
                <td>2</td>
                <td>invalid</td>
                <td>檢核失敗</td>
            </tr>
            <tr valign="top">
                <td>999</td>
                <td>exception</td>
                <td>系統錯誤</td>
            </tr>
        </tbody>
    </table>
## Note
- all files in `/src` are completed by Sam Lin
