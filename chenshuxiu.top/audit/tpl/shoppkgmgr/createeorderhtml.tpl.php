<!--100*150,90-->
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: "simsun";
        }

        .print_paper {
            font-size: 14px;
            border: none;
            border-collapse: collapse;
            width: 375px;
            margin-top: -1px;
            table-layout: fixed;
        }

            .print_paper td {
                border: solid #000 1px;
                padding: 0 5px;
            }

        .table_first {
            margin-top: 0;
        }

        .print_paper .x1 {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 5px;
            line-height: 0.95;
            font-family: "Microsoft YaHei";
        }

        .print_paper .x4 {
            font-size: 20px;
            font-weight: bold;
            font-family: "Microsoft YaHei";
        }

        .print_paper .xx8 {
            font-size: 8px;
            line-height: 0.8;
        }

        .print_paper .xx10 {
            font-size: 10px;
        }

        .print_paper .xx12 {
            font-size: 12px;
            font-weight: bold;
        }

        .print_paper .xx14 {
            font-size: 14px;
            font-weight: bold;
            font-family: "SimHei";
        }

        .print_paper .xx16 {
            font-size: 16px;
            font-weight: bold;
            font-family: "Microsoft YaHei";
        }

        .print_paper .xx48 {
            font-size: 40px;
            font-weight: bold;
            text-align: center;
            font-family: "Microsoft YaHei";
        }

        .no_border {
            width: 100%;
            height: 100%;
            font-size: 14px;
        }

            .no_border td {
                border: none;
                vertical-align: top;
            }

        .print_paper .fwb {
            font-weight: bold;
        }

        .print_paper .f24 {
            font-family: "Arial";
            font-size: 24pt;
        }

        .print_paper .p0 {
            padding: 0;
        }
            /*增加类*/
            .print_paper .p0 .sp {
                position: absolute;
                left: 3px;
                top: 110px;
            }

        .print_paper .ovh {
            overflow: hidden;
        }

        .print_paper .ov {
            overflow: visible;
        }

        .print_paper .f10 {
            font-size: 10px;
        }

        .print_paper .f13 {
            font-size: 13px;
        }

        .print_paper .bln {
            border-left: none;
        }

        .print_paper .brn {
            border-right: none;
        }
    </style>

    <table class="print_paper table_first topLogo">
        <tbody><tr height="52">
            <td>
                <table class="no_border">
                    <tbody><tr>
                        <td style="vertical-align:middle;"><img class="logo" height="35" src="data:image/png;base64,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" alt=""></td>
                        <td style="vertical-align:middle;" colspan="2">
                            <!--&nbsp;-->
                            <div class="f24" style="font-weight:bold;display:none">POD</div>
                        </td>
                        <!--<td style="vertical-align:middle;">&nbsp;</td>-->
                        <td style="vertical-align:middle;text-align:right;"><img height="35" class="phone" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAAA8CAMAAADWtUEnAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABhQTFRFIiIi1NTUjo6OsbGxYWFh7u7uAAAA////lVpTKwAABPNJREFUeNrsWYvO8yYM9Y30/d94NgZig5t+0/5NlfZFqpKcgDm+gA2F15df8Evwl+D3EUQi0YuIv5MgClx+CX8fQRaBye+6AITaVxFEufaLzj7UW0n8kvstoUyuLSUpNTrlAuFbggwHvwtksyHdn/CZIAZxsMKlRtPgcchIEAt+1jzxS1TwkSCXrqjRoHdWHXYViA6aUaGNCf6c4NTmRygUBN1+tOmyxSEvz1Cybu+7zyfWeGq3ceQJhfky3EgnwT624GGO5GS57UbRAF3qPpk4exUeUL7HaZBMuB6aLOJtd/KK5Ra1g8A9u+VcK65KgYBGbaUmSGHs04QpUvC1C2ofCEpJUHaCr+P5Jjis5uvy2yj0Dy2y7S/4E4LwgAZZPshhQQzObPJuqSkI4h2/H1wsD2jb/XdMEg5M+HommPzNeZHY8kD0JT+htGYf1csMhXUJi3wSOVEcgI6YOFKPFOv9gY5XX4WLhZqi+NPF0IInoIWgOAnOxhmnKllGVK7HVEfRm2fOm2PKitSZOguCt1loOb4V2Tyjd+SnMu8kaE4+VsKpEz5WOzjTJJ6FRWnBgFK1rBWTZARAf4UHm6zPOfQRUnMqQ7NCh/lGDIYgLAh2f6oHhaRIdnynl1zQFFkt23UrPW+0pczOeUCoSi1TC9XMy2VxCrYOarmK7/PXtiY3qGrfiMZelDwDR4gmC4+pANXuhD+lh2Cu6xnlzCnVSXU1vQKmmwvKnYmUOwIoaX9AUwJ4Q3CvOTlYForsMDyEfyOr/UOC2+IHbO1bN6y8LQCOL1TutFrp4htNvdpVEzxKBCBmeRuBKZRp09IriK3OHZO0QjFmA0k6QrVjv/fFB79RDGyZVOXxitgJ84auwqdA7ww1dr4rXcLzrviwX510y7zIZUVUo0dypXJffNrw8G9dFJRbPS4TdI3uW3J6c7LQ9q07yDZN6/1yWSlgqSe+0T66Lxpln1kcjVgczfCMPtoOxGYuTWXLjMl0UFajK/lBhovTLeUAeolUxfF3HGAi64Xt9fpWgr9HwL8Efwn+CYKWndo3E9RNi3y1BQH+axezpVHREg8vzXc43ynAsxIExLwZBXtBKx9IK1BNQ55Q5RIrSMXTv6VbWbdZ+1ltYf37aYCNDCyhiI/1oMkClUSXJlzqQ+lYEXZ+ICBN7E+UVbxqYtRB7QeqitEVq/SkJ22FxWFFcd6cn8kyla09W6ljD1eXdrpYzWYtzFzUbxdjH3LBLpR6CR1cDKavvjcznbg6vV1/6xqqoGZ3buM2FDPPdaVeNiS7vSVIh7TL0KIEYAylN3unCA8P9xrMXntxx20UP617vw3pOHXieUAGftQB68RDxpGz71a0F7tm/IYgmytoBp0+QPdFhOduZOiI9q8jNvOwSDdWbxQIoou1Pyd78KmtcYXY8HRzyZ8JvqzIUrn+X4R6VuMDMrwotuzi5YPuLZtiLzc+jsm3qlK5bzOqb4U+ERSbbHC5VLU7uUFu2DyvltItcSJo4zWxYAefWTaloU+SHgzaHYGbzlN1w7qBRac+y+ivrT4RJI9Rj4irn75QgtFZXymM55aHOydwZbyNE5zHuxBvONefzn60/0SwSV+tfFdoM4Fkg+0PSpb1eNtQpGmT3o5sVnsb9iMUFF+RxHOP33r/+YW8vZ1YaScM0v+VdPAnk8z/kyDRbz34PddfAgwAfP7m2QIPxogAAAAASUVORK5CYII=" alt=""></td>
                    </tr>
                </tbody></table>
            </td>
        </tr>
    </tbody></table>
    <table class="print_paper">
        <tbody><tr height="75">
            <td class="p0" width="249" style="text-align:center;">
                <img width="210" height="50" src="" id="imgcode_big_<?= $shopPkg->express_no ?>">
                <span class="sp" style="font-size:11px;"></span>
                <br><div style="font-size: 11px;font-weight:bold;"><?= $eorder["LogisticCode"] ?></div>
            </td>
            <td class="xx16 p0">
                <div style="height: 74px;">
                    <div style="font-size:20pt; text-align:center;">T6</div>
                    <span class="xx10">目的地:</span><span class="f24"><?= $eorder["DestinatioCode"] ?></span>
                </div>
            </td>
        </tr>
    </tbody></table>

    <table class="print_paper" height="60">
        <tbody><tr>
            <td width="50" style="padding:0;" class="xx16 brn">收方:</td>
            <td class="bln">
                <div style="height: 59px;overflow:hidden;">
                    <?= $shopaddress->xprovince->name ?> <?= $shopaddress->xcity->name ?> <?= $shopaddress->xcounty->name ?> <?= $shopaddress->content ?><br>
                    <?= $shopaddress->linkman_name ?>&nbsp;&nbsp;<?= $shopaddress->linkman_mobile ?>
                </div>
            </td>
        </tr>
    </tbody></table>
    <table class="print_paper" height="98">
        <tbody><tr>
            <td rowspan="2" style="vertical-align:top;" class="f13">
                月结帐号：0100026792
                <br>
                支付方式：寄付月结
                <br>
                <div style="display: None">声明价值：0元</div>
                <br>
                <div style="display: none">签单返回单号：${SignWaybillCode}</div>
                <br>
                <div style="display: none">操作要求：${OperateRequire}</div>
            </td>
            <td height="54">代收货款：0 元<br>卡号：</td>
        </tr>
        <tr>
            <td>运费：-<br>费用合计：-</td>
        </tr>
    </tbody></table>

    <table class="print_paper" height="52">
        <tbody><tr>
            <td class="xx14 brn" width="50" style="padding:0; width:40px;">
                寄方:
            </td>
            <td class="ov bln">
                <div class="xx10" style="width:115px; height:50px;">
                    北京市 西城区  华远北街通港大厦708<br>
                    <span style="padding-right: 5px;">方寸医生</span> 18510542099<br>
                    <span class="xx10" style="margin-left: -45px;">原寄地：<?= $eorder["OriginCode"] ?></span>
                </div>
            </td>
            <td class="xx10" width="86">收件员：<br>寄件日期：<?= date("Y-m-d") ?></td>
            <td class="xx10">收方签署:<br><br>日期：</td>
        </tr>
    </tbody></table>

    <table class="print_paper table_first" height="60">
        <tbody><tr height="66">
            <td>
                <img class="logo" height="30" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAAA8CAMAAADWtUEnAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABhQTFRFtrW1YVxcAQEB/Pz8/f39/v7+AAAA////LcREfwAACbFJREFUeNrMWouW3SgMg9gO///HiyQbSOaxbU+33Zx25uYmJMLYsmymjf/50V7nER78FTFifxn8P/Drt706dPwcQMPh5n6cWl1ztzCL34bPcfw0QBcEs9Zaz2N+JExA/G0Af9GCtJ1NaNd1H8d1TZgTY/z7lP9bgA50rV+J6rqBM6EKY/hfAzjDI2A84rkEjzDvZcyrNx9wxW8cS+/8gbfvWyKOz66PFQYb4PR/H4fxyn7CKNCEOL6JlF8HiMiE++ABDl83jW6PAAa86yow99MLZdQJ0fz3AwS6ZcH52WvsBjht2q57WQ0uh/BVMCdqmbWNEd+9lovzM5R5zmRDewDELZNJWl9B25qd7IO4lnXnGs87reb+CjEyQRxnNXt/mu4xzIpy5+fAjzzBfS39b+Kbbx5a5C5wMc5lUvjMBZ6z6tNfCWMyd19H4+cWmOg8rC0aNU0x7yLbHsMixGukCeOLcNIMsFqR+lzfSwhFeC8XwmeMJD7cmXw+bHtrt86fkb/7Efwj+nLmZvSmNWzsMw6uk/mSAjhH4HF4u3GG9vKMeWJ5Me/E7ECKJ0C+aIJvsEAbffnz/TjDLNvm116+P6/jSnHcBOgF0E03zQANQJkGjUoaEQWQRoN5rpwdxtmleNLi4Vpv/Ambcdk6n+y0auMJ57CH5ShkiOk7ddKvw4LwwFsBimTsIBJmcic94QclBCA2BvrFUMBAGU33JlxeH0MLPb3v0oJftZoF0BiazktNfm55HfHbvAAOMQxMzGgiJigXE2XmNw42jSYSz+gSJCsmxeoy2KfTCeCQT1wFkJ5ZADP8NBN49BCQeWLIutuCWjfY5dQr08/sQRDDZOt84ALYLHWaHkRf8gRoL4DXBqhhwSW+GZtcA4WwQmM0RWtTwkVw4suW5DDoh26pvMSN8m/StZb4SmeyWgqu+bbgscRysacP1hMZPBkkojoQJADiWcoRJrauvAFOjta29OIwuut8WZgXwKQImlzr5ZYAm+KiZ8jQEBnFFcTzHTsJJBWLcmhB+na903NoMYDWZ2U5+W/n0wDCEyAPOU5aymnBHIRhkTSjG2U0DRuk/ppkDG8JscN0k/cVfHhPG5ZBep8Ar21BAsw4brXE8yMPeCw58BZD9CU5pmVIM5RufaQZahjplIYGBLc8wYJGWrCoY3jSTZdASAumm5VSkFHFLe8ozhXvYJCelu9F7zcTOgPoiGKHxJy/msgZq4h1lgkAML1F9pUtGz4hQZYFFW+N7BnRtQKgvgKIvI21SDbYNEMTTXu6lj6TYQGk8qFIIG/OcTBgpF1AMRNgeAEclVAyf1iGxGIs1AOjPLwzzLTEnvVfO4jaK4r1tJ5ZbbkJSE3DJE5oimkDsbT4mhYMW2u2ACqxTYcdxQvibAr+DbB8MHOWHyqhJ8Al2ngmx6xcXMM6dEzXvBqlXk8y1RJnwt8xgikq43kUQCezQwYWQH35uVhIefAJQJMFHmqmVCjcZl/IXBy2ABrMk0HSqDzNlgWpFpxZb0/IP5Nb8TCklfzWNVPotCfAl8xZ+ATQD5/yohlCtAMgBSclxIpsWfAtWJkNlmCd4YYMbpZn8DjmpGPYsEpUJd4ljaKIegOEjYrVLwrDBbBlNnP3zTNmjzo+2caTPiJ7FZJqroLDMoe/FL/n9wrO1W6BWIjt9OFS3FnDAU68ACp7lAUpywyEHwyhaS4LaSDUpqk81jWZ0sUqFCHwaOc0wYTSm9SkkbNkFJ9LTGnfM9t1EewLoB8AOadUZ7tAShxkAlNFYtKZdOHsAM1LZinnQHaf17IvH8w1qAK5v5a4yQBriXF7Vxl1tJpCj2GMDZYxSQlEM6x8YF7KGREfpssJZQY0VqAnzSDZU/DLiplTi2Ya8Lk/oxifkVL7zGL3qnVCVUQGb7E8FwcxMG9QvT0fgUzD9qNbTvU6tUemus2D8ytTY0QI+47i7BzacglakAAnzzKnkWObpXNl1X8tTSAHAyzvR8+iA7oigTSzAbpo5swkjBI6s1B0PwF6+vgzkyRAA8Bcm5LaZxOlpZ6g4egPE1DWxbjf4Vfd9oHVkg/uXByoySG1d5m3ltiW4y9tMfwAWAxL5QCA8shrF28U8fDgGWtaYvZWRgYvPdnOI4smI8CrVIaSdUjY9SOTcKJR8pbz8XOJpfwLYEUxvtBJqMgxBG+u2a3MM+r+ZgeB9yw749SD/MR5tCp/F8Dl6keMPCzIBb4SYPWAjiAheVvmHNbIKKG7yNmTJ0q5UxGkD+YrWZtK8F9cmkcUx2o+vRX1AfDqG6B7hWpP3UwYytDemaun40JA0LaVpBXdcmb4oN6SNYlVeZddy6pjE2DUzVmT+NcAlYN2kKhnB35suNl7VTKqPMAHFFxaxKxpBZCL17bBzm5qlcEqI9WeLFXPr762IExSbdoijWxSYlm7mt+8ElFgMJiGndMwpcC23epSYa1OYJWYVhbc+zt91cXjC4BjBwnmV6FZ5N3ZwMgCOnUEhT0Hd9qJRcPRWWBzDPcHe1jJrCpEulJI7F5Hyl1pl698cLwzCTs+UQCr19IyviMBNpFhF7Wt7lbWqRi5Wx1KHPXw2G9UiMWnFrzXErd2ejyq0g2QLTwo/SwAFfUAKCml6o+dhQzN3d1K1cSmOxlMvS5jdRgVw12IDx40mA//qj1/PY/UIqpyqH4JtJcPQrRQalf0XB/6gzeqfPZimJAFIf8HpdPInKkaf6g+6QLocCv51FcA6ZfZqrFUM8ytyiQqNevg4i8LihQ69aq62O99Pv+0w2qKOdBZ9bCbxWp4PoNEJUSuQMsOFXKdyyWqHGqH4jqa6O2SPs0WetiHXbnqURubn54AGc5c4lv8XpQsbj+ChIK9sZivZHFX6azga+0hc2x3+bF7gpqFN+TG4Qd0pB+kakiqyq1NkhfZCy3RxjIpacZ5RaWWNo9YT/E9Z8rVAG0AnFdYFx9d8rEKJmJs6UzOnkdt4bGYqupGm96vHZudtT2Zytf2iFG1xNqKtmwQ0CUogs4L+KadxVWr3a7aga3jOnbwsqD+oR2ksw2frhTHhVXEsOjSt++9j21B1a6Z5O77y726g7V/AKE/N1xY2dUWkbYN1+aep55TK1xu8drtzJrzXol89RyPDQ97V8Pf7/pzt+JtFoqd/JOAqH2OtbGoOtFlhwPg4Fxyw7hUmdzuKs9kb3vEiJ/aJIyPLhprY3hstRjnLmhefO+42yEXKir2BqNV9+Bv/VkKcl790UJf66r9oqYOhfvfBDi8dkLPP/uoP/oYHt/9OcAfARjsChQbfNgf9wj/uwBX8fbcdt5OG/GHAf4jwABZwKzJwOZruQAAAABJRU5ErkJggg==" alt="">
                <img class="phone" height="30" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAAA8CAMAAADWtUEnAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABhQTFRFIiIi1NTUjo6OsbGxYWFh7u7uAAAA////lVpTKwAABPNJREFUeNrsWYvO8yYM9Y30/d94NgZig5t+0/5NlfZFqpKcgDm+gA2F15df8Evwl+D3EUQi0YuIv5MgClx+CX8fQRaBye+6AITaVxFEufaLzj7UW0n8kvstoUyuLSUpNTrlAuFbggwHvwtksyHdn/CZIAZxsMKlRtPgcchIEAt+1jzxS1TwkSCXrqjRoHdWHXYViA6aUaGNCf6c4NTmRygUBN1+tOmyxSEvz1Cybu+7zyfWeGq3ceQJhfky3EgnwT624GGO5GS57UbRAF3qPpk4exUeUL7HaZBMuB6aLOJtd/KK5Ra1g8A9u+VcK65KgYBGbaUmSGHs04QpUvC1C2ofCEpJUHaCr+P5Jjis5uvy2yj0Dy2y7S/4E4LwgAZZPshhQQzObPJuqSkI4h2/H1wsD2jb/XdMEg5M+HommPzNeZHY8kD0JT+htGYf1csMhXUJi3wSOVEcgI6YOFKPFOv9gY5XX4WLhZqi+NPF0IInoIWgOAnOxhmnKllGVK7HVEfRm2fOm2PKitSZOguCt1loOb4V2Tyjd+SnMu8kaE4+VsKpEz5WOzjTJJ6FRWnBgFK1rBWTZARAf4UHm6zPOfQRUnMqQ7NCh/lGDIYgLAh2f6oHhaRIdnynl1zQFFkt23UrPW+0pczOeUCoSi1TC9XMy2VxCrYOarmK7/PXtiY3qGrfiMZelDwDR4gmC4+pANXuhD+lh2Cu6xnlzCnVSXU1vQKmmwvKnYmUOwIoaX9AUwJ4Q3CvOTlYForsMDyEfyOr/UOC2+IHbO1bN6y8LQCOL1TutFrp4htNvdpVEzxKBCBmeRuBKZRp09IriK3OHZO0QjFmA0k6QrVjv/fFB79RDGyZVOXxitgJ84auwqdA7ww1dr4rXcLzrviwX510y7zIZUVUo0dypXJffNrw8G9dFJRbPS4TdI3uW3J6c7LQ9q07yDZN6/1yWSlgqSe+0T66Lxpln1kcjVgczfCMPtoOxGYuTWXLjMl0UFajK/lBhovTLeUAeolUxfF3HGAi64Xt9fpWgr9HwL8Efwn+CYKWndo3E9RNi3y1BQH+axezpVHREg8vzXc43ynAsxIExLwZBXtBKx9IK1BNQ55Q5RIrSMXTv6VbWbdZ+1ltYf37aYCNDCyhiI/1oMkClUSXJlzqQ+lYEXZ+ICBN7E+UVbxqYtRB7QeqitEVq/SkJ22FxWFFcd6cn8kyla09W6ljD1eXdrpYzWYtzFzUbxdjH3LBLpR6CR1cDKavvjcznbg6vV1/6xqqoGZ3buM2FDPPdaVeNiS7vSVIh7TL0KIEYAylN3unCA8P9xrMXntxx20UP617vw3pOHXieUAGftQB68RDxpGz71a0F7tm/IYgmytoBp0+QPdFhOduZOiI9q8jNvOwSDdWbxQIoou1Pyd78KmtcYXY8HRzyZ8JvqzIUrn+X4R6VuMDMrwotuzi5YPuLZtiLzc+jsm3qlK5bzOqb4U+ERSbbHC5VLU7uUFu2DyvltItcSJo4zWxYAefWTaloU+SHgzaHYGbzlN1w7qBRac+y+ivrT4RJI9Rj4irn75QgtFZXymM55aHOydwZbyNE5zHuxBvONefzn60/0SwSV+tfFdoM4Fkg+0PSpb1eNtQpGmT3o5sVnsb9iMUFF+RxHOP33r/+YW8vZ1YaScM0v+VdPAnk8z/kyDRbz34PddfAgwAfP7m2QIPxogAAAAASUVORK5CYII=" alt="">
            </td>
            <td style="text-align:center;">
                <img width="177" height="37" id="imgcode_small_<?= $shopPkg->express_no ?>" src="">
                <?= $eorder["LogisticCode"] ?>
            </td>
        </tr>

    </tbody></table>
    <table class="print_paper ">
        <tbody><tr height="56">
            <td class="brn" style="padding:0; width:16px;">寄<br>方:</td>
            <td class="xx10 bln">
                <div style="height: 55px;overflow:hidden;">
                    北京市 西城区  华远北街通港大厦708<br>
                    方寸医生&nbsp;&nbsp;18510542099
                </div>

            </td>
            <td style="padding:0; width:16px;" class="fwb brn">收<br>方:</td>
            <td class="xx10 bln">
                <div style="height: 55px;overflow:hidden;">
                    <?= $shopaddress->xprovince->name ?> <?= $shopaddress->xcity->name ?> <?= $shopaddress->xcounty->name ?> <?= $shopaddress->content ?><br>
                    <?= $shopaddress->linkman_name ?>&nbsp;&nbsp;<?= $shopaddress->linkman_mobile ?>
                </div>
            </td>
        </tr>
    </tbody></table>

    <table class="print_paper">
        <tbody><tr height="18">
            <td width="36" style="padding:0; text-align:center;">数量</td>
            <td width="254" style="padding:0; text-align:center;">托寄物</td>
            <td style="padding:0; text-align:center;">备注</td>
        </tr>
        <tr height="34">
            <td>&nbsp;</td>
            <td>商品</td>
            <td>
                <div class="f10 ovh" style="height:33px">

                </div>
            </td>
        </tr>
        <tr height="50">
            <td colspan="2">
                <table class="no_border">
                    <tbody><tr>
                        <td>订单号</td>
                        <td class="xx14" style="vertical-align:middle; text-align:center;">
                            <?= $eorder["OrderCode"] ?>
                        </td>
                    </tr>
                </tbody></table>
            </td>
            <td style="text-align:center;">费用合计：<br>- 元</td>
        </tr>
    </tbody></table>

<table class="print_paper table_first print_paper_3">
    <tbody>
        <tr height="30">
            <td class="xx10">
                <div style="padding-left:5px;">收件人:<span class="linkmanName"><?= $shopaddress->linkman_name ?></span></div>
            </td>
            <td class="xx10 bln">
                <div style="padding-left:5px;">订单号:<span class="OrderCode"><?= $eorder["OrderCode"] ?></span></div>
            </td>
            <td class="xx10 bln">
                <div style="padding-left:5px;">快递单:<span class="LogisticCode"><?= $eorder["LogisticCode"] ?></span></div>
            </td>
        </tr>
    </tbody>
</table>
<table class="print_paper print_paper_3">
    <tbody>
        <tr height="190" style="overflow:hidden;">
            <td class="xx10">
                <div style="padding:0px 10px;" class="medicineStr"><?= $shopPkg->getTitleAndCntOfShopProducts('<br/>') ?></div>
            </td>
        </tr>
    </tbody>
</table>
