<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>中壢過嶺親子館 | 115年度入館登記</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            text-align: center;
            background-color: #f4f7f6;
            color: #333;
        }

        .container1 {
            max-width: 1000px;
            max-height: 500px;
            margin: auto;
            padding: 25px;
        }

        .container2 {
            max-width: 900px;
            margin: auto;
            padding: 25px;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #57c05f;
            margin-bottom: 5px;
        }

        h3 {
            font-size: 14px;
            color: #777;
            margin-top: 0;
        }

        input,
        select {
            width: 95%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }

        .label-group {
            text-align: left;
            margin: 15px 5% 5px 5%;
            font-weight: bold;
            color: #555;
            border-left: 4px solid #468b4c;
            padding-left: 8px;
            margin-top: 20px;
        }

        .flex-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 5px 5%;
        }

        button {
            width: 96%;
            padding: 15px;
            background: #48a187;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 17px;
            font-weight: bold;
            margin-top: 20px;
        }

        .hidden {
            display: none;
        }

        /* 修正後的語言清單樣式 */
        .lang-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            text-align: left;
            margin: 10px 8%;
        }

        .lang-item {
            display: flex;
            align-items: center;
            font-size: 15px;
            cursor: pointer;
        }

        .lang-item input {
            width: auto;
            margin: 0 8px 0 0; /* 移除寬度 95% 影響 */
        }

        /* 動態幼兒區塊樣式 */
        .child-info-block {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 5%;
            border: 1px dashed #57c05f;
            text-align: left;
        }

        .child-title {
            color: #468b4c;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 10px;
        }

        .hint-text {
            font-size: 0.85em;
            color: #888;
            text-align: left;
            margin-left: 5%;
        }
    </style>
</head>

<body>
    <div class="container1">
        <div class="box01" style="background-image: url(./img/親子館.jpg); 
        height: 500px;
        background-size: cover; background-position: center;"></div>
    </div>

    <div class="container2">
        <h2>中壢過嶺親子館</h2>
        <h3>115年度入館登記</h3>

        <form id="checkinForm">
            <input type="tel" id="phone" placeholder="手機號碼 (Phone Number)" required>

            <div id="new_member_fields" class="hidden">
                <p style="background: #fff3cd; padding: 10px; border-radius: 8px; font-size: 13px;">歡迎首次入館，請填寫基本資料：</p>
                <input type="text" id="parent_name" placeholder="填答者姓名 (Respondent Name)">

                <div class="label-group">您來自哪裡？</div>
                <select id="district">
                    <option value="">請選擇區域</option>
                    <option value="桃園市中壢區">桃園市中壢區 Zhongli Dist</option>
                    <option value="桃園市桃園區">桃園市桃園區 Taoyuan Dist</option>
                    <option value="桃園市平鎮區">桃園市平鎮區 Pingzhen Dist</option>
                    <option value="桃園市八德區">桃園市八德區 Bade Dist</option>
                    <option value="桃園楊梅區">桃園楊梅區 Yangmei Dist</option>
                    <option value="桃園市蘆竹區">桃園市蘆竹區 Luzhu Dist</option>
                    <option value="桃園市大溪區">桃園市大溪區 Daxi Dist</option>
                    <option value="桃園市龍潭區">桃園市龍潭區 Longtan Dist</option>
                    <option value="桃園市龜山區">桃園市龜山區 Guishan Dist</option>
                    <option value="桃園市大園區">桃園市大園區 Dayuan Dist</option>
                    <option value="桃園市觀音區">桃園市觀音區 Guanyin Dist</option>
                    <option value="桃園市新屋區">桃園市新屋區 Xinwu Dist</option>
                    <option value="桃園市復興區">桃園市復興區 Fuxing Dist</option>
                    <option value="其他縣市">其他縣市 Other City</option>
                </select>

                <div class="label-group">主要目的？</div>
                <select id="purpose">
                    <option value="玩玩具">玩玩具 Play with toys</option>
                    <option value="上課/參加活動">上課/參加活動 Classes/activities</option>
                    <option value="借用/歸還玩具">借用/歸還玩具 Borrow/return toys</option>
                    <option value="問題諮詢">問題諮詢 Advisory</option>
                    <option value="其他">其他 Other</option>
                </select>

                <div class="label-group">您與幼兒的關係？</div>
                <select id="relationship">
                    <option value="父母與孩子">父母與孩子 Parents and children</option>
                    <option value="祖父母/孫子女">祖父母/孫子女 Grandparents/grandchildren</option>
                    <option value="親屬">親屬 Relatives</option>
                    <option value="社福/教育機構">社福/教育機構 Social Welfare/Educational Institution</option>
                    <option value="托育人員/保母">托育人員/保母 Child Carer/Babysitter</option>
                    <option value="其他">其他 Other</option>
                </select>

                <div class="label-group">大人人數 (含填答者)</div>
                <div class="flex-row"><span>男性 / Male:</span><input type="number" id="adult_male" value="0" min="0" style="width: 80px;"></div>
                <div class="flex-row"><span>女性 / Female:</span><input type="number" id="adult_female" value="0" min="0" style="width: 80px;"></div>

                <div class="label-group">幼兒人數與資訊</div>
                <div class="flex-row">
                    <span>入館幼兒總數:</span>
                    <input type="number" id="child_count" value="0" min="0" max="5" style="width: 80px;" onchange="generateChildFields(this.value)">
                </div>
                
                <div id="dynamic_child_container"></div>

                <div class="label-group">填答者身分別 (選填)</div>
                <select id="respondent_type">
                    <option value="">無</option>
                    <option value="原住民">原住民</option>
                    <option value="新住民">新住民</option>
                </select>

                <div class="label-group">常用語言 (可多選)</div>
                <div class="lang-grid">
                    <label class="lang-item"><input type="checkbox" name="lang" value="國語"> 國語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="台語"> 台語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="客語"> 客語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="英語"> 英語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="越南語"> 越南語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="印尼語"> 印尼語</label>
                </div>
            </div>
            <button type="submit" id="submit_btn">確認報到</button>
        </form>

        <div id="result" class="hidden">
            <div class="success-icon">✓</div>
            <h2 style="color: #28a745;">報到成功！</h2>
            <p id="welcome_user" style="font-size: 20px; font-weight: bold;"></p>
            <p style="color: #777; margin-top: 20px;">系統將於 10 秒後自動重置...</p>
        </div>
    </div>

    <script>
        // 動態產生幼兒資訊欄位
        function generateChildFields(count) {
            const container = document.getElementById('dynamic_child_container');
            container.innerHTML = ''; 
            
            for (let i = 1; i <= count; i++) {
                const div = document.createElement('div');
                div.className = 'child-info-block';
                div.innerHTML = `
                    <span class="child-title">第 ${i} 位幼兒資料</span>
                    <input type="text" class="c_name" placeholder="幼兒姓名" required>
                    <div style="text-align:left; font-size:13px; color:#666; margin-left:2%;">生日：</div>
                    <input type="date" class="c_birthday" required>
                    <select class="c_gender" required>
                        <option value="">請選擇性別</option>
                        <option value="男">男 (Male)</option>
                        <option value="女">女 (Female)</option>
                    </select>
                `;
                container.appendChild(div);
            }
        }

        const form = document.getElementById('checkinForm');
        form.onsubmit = async (e) => {
            e.preventDefault();
            const phone = document.getElementById('phone').value;
            const langs = Array.from(document.querySelectorAll('input[name="lang"]:checked')).map(el => el.value).join(',');

            // 組合多位幼兒資訊
            const childNames = Array.from(document.querySelectorAll('.c_name')).map(el => el.value).join('|');
            const childBirthdays = Array.from(document.querySelectorAll('.c_birthday')).map(el => el.value).join('|');
            const childGenders = Array.from(document.querySelectorAll('.c_gender')).map(el => el.value).join('|');

            const data = new URLSearchParams({
                phone: phone,
                name: document.getElementById('parent_name').value,
                district: document.getElementById('district').value,
                purpose: document.getElementById('purpose').value,
                relationship: document.getElementById('relationship').value,
                adult_male: document.getElementById('adult_male').value,
                adult_female: document.getElementById('adult_female').value,
                child_count: document.getElementById('child_count').value,
                child_name: childNames,
                child_birthday: childBirthdays,
                child_gender: childGenders,
                respondent_type: document.getElementById('respondent_type').value,
                languages: langs
            });

            const response = await fetch('checkin_logic.php', {
                method: 'POST',
                body: data
            });
            const res = await response.json();

            if (res.status === 'need_register') {
                document.getElementById('new_member_fields').classList.remove('hidden');
                document.getElementById('parent_name').required = true;
                document.getElementById('district').required = true;
                document.getElementById('submit_btn').innerText = "完成註冊並報到";
                document.getElementById('new_member_fields').scrollIntoView({ behavior: 'smooth' });
            } else if (res.status === 'success') {
                form.classList.add('hidden');
                document.getElementById('result').classList.remove('hidden');
                document.getElementById('welcome_user').innerText = `歡迎光臨，${res.user_name} 家長`;
                setTimeout(() => { location.reload(); }, 10000);
            } else {
                alert(res.message);
            }
        };
    </script>
</body>

</html>