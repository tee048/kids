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

        .box01 {
            background-image: url(img/親子館.jpg);
            background-size: cover;
            background-position: center;
            width: 100%;
            height: 400px;

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
            color: #2c9635;
            margin-bottom: 5px;
            font-size: 34px;
        }

        h3 {
            font-size: 20px;
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
            font-size: 20px;
            box-sizing: border-box;
        }

        .label-group {
            text-align: left;
            margin: 15px 5% 5px 5%;
            font-weight: bold;
            font-size: 20px;
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
            font-size: 18px;
        }

        button {
            width: 96%;
            padding: 15px;
            background: #48a187;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 25px;
            font-weight: bold;
            margin-top: 20px;
        }

        .btn-secondary {
            background: #6c757d;
            margin-top: 10px;
            font-size: 25px;
        }

        .hidden {
            display: none;
        }

        .lang-item {
            display: flex;
            align-items: center;
            font-size: 20px;
            padding: 10px 0;
        }

        .lang-item input {
            width: 30px;
            height: 30px;
            margin-right: 15px;
        }

        .child-info-block {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 5%;
            border: 1px dashed #57c05f;
            text-align: left;
        }

        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 10px;
        }

        .lang-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            text-align: left;
            margin: 10px 8%;
            font-size: 16px;
        }

        @media screen and (max-width:768px) {
            body {
                text-align: center;
                padding: 30px;
            }


            .container1 {
                padding: 0;
                width: 100%;
                height: auto;
            }


            .box01 {
                background-image: url(img/親子館.jpg);
                background-position: center;
                background-size: contain;
                width: 100%;
                height: auto;
                aspect-ratio: 800 / 500;

            }

            .container2 {
                max-width: 100%;
                height: auto;
                margin: 20px;
                padding: 25px;
                border-radius: 20px;
                background: white;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }

            h2 {
                color: #2c9635;
                margin-bottom: 5px;
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <div class="container1">
        <div class="box01"></div>
    </div>

    <div class="container2" id="checkinArea">
        <h2>中壢過嶺親子館</h2>
        <h3 id="form_title">115年度入館登記</h3>

        <form id="checkinForm">
            <input type="tel" id="phone" placeholder="手機號碼 (Phone Number)" required>

            <div id="new_member_fields" class="hidden">
                <p style="background: #fff3cd; padding: 10px; border-radius: 8px; font-size: 20px;">歡迎首次入館，請填寫基本資料：</p>
                <input type="text" id="parent_name" placeholder="填答者姓名 (Respondent Name)">

                <div class="label-group">您來自哪裡？</div>
                <select id="city_region" onchange="updateSubDistrict()">
                    <option value="">請選擇區域</option>
                    <option value="桃園市中壢區">桃園市 中壢區</option>
                    <option value="桃園市其他行政區">桃園市 其他行政區</option>
                    <option value="其他縣市">其他縣市</option>
                </select>
                <select id="sub_district" class="hidden"></select>
                <input type="text" id="other_city_input" class="hidden" placeholder="請輸入您的縣市與區域">

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
                    <option value="祖父母/孫子女">祖父母/孫子女 Grandparents and grandchildren</option>
                    <option value="親屬">親屬 Relatives</option>
                    <option value="社福/教育機構">社福/教育機構 Social Welfare/Educational Institution</option>
                    <option value="托育人員/保母">托育人員/保母 Child Carer/Babysitter</option>
                    <option value="其他">其他 Other</option>
                </select>
                </select>

                <div class="label-group">大人人數 (含填答者)</div>
                <div class="flex-row"><span>男性 / Male:</span><input type="number" id="adult_male" value="0" min="0" style="width: 80px;"></div>
                <div class="flex-row"><span>女性 / Female:</span><input type="number" id="adult_female" value="0" min="0" style="width: 80px;"></div>

                <div class="label-group">幼兒人數與資訊</div>
                <div class="flex-row"><span>入館幼兒總數:</span><input type="number" id="child_count" value="0" min="0" max="5" style="width: 80px;" onchange="generateChildFields(this.value)"></div>
                <div id="dynamic_child_container"></div>

                <div class="label-group">填答者身分別 Respondent Type (若都不是，可不填)</div>
                <div class="lang-grid">
                    <label class="lang-item"><input type="radio" name="respondent_type" value="原住民"> 原住民</label>
                    <label class="lang-item"><input type="radio" name="respondent_type" value="新住民"> 新住民</label>
                </div>

                <div class="label-group">常用語言 Common Languages (可多選)</div>
                <div class="lang-grid">
                    <label class="lang-item"><input type="checkbox" name="lang" value="國語"> 國語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="台語"> 台語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="客語"> 客語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="英語"> 英語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="日語"> 日語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="韓語"> 韓語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="廣東話"> 廣東話</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="印尼語"> 印尼語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="越南語"> 越南語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="法語"> 法語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="德語"> 德語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="西班牙語"> 西班牙語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="阿拉伯語"> 阿拉伯語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="其他語言"> 其他語言</label>
                </div>
            </div>
            <button type="submit" id="submit_btn">確認報到</button>
        </form>
    </div>

    <div id="old_member_selection" class="container2 hidden" style="text-align: left; padding: 25px;">
        <h2 style="text-align: center;">請勾選本次入館人員</h2>
        <div class="label-group">本次入館家長 (可多選)</div>
        <div id="parent_checkboxes" style="margin: 10px 8%;"></div>
        <div class="label-group">本次入館幼兒 (可多選)</div>
        <div id="child_checkboxes" style="margin: 10px 8%;"></div>
        <div style="margin-top: 25px; border-top: 1px dashed #ccc; padding-top: 15px;">
            <div class="label-group">新增其他入館人員 (選填)</div>
            <input type="text" id="add_new_parent" placeholder="新增其他家長姓名" style="width: 95%;">
            <div style="background: #fdfaf0; padding: 10px; border-radius: 8px; margin-top: 10px;">
                <input type="text" id="add_new_child" placeholder="新增其他幼兒姓名" style="width: 100%;">
                <div style="display: flex; gap: 10px; margin-top: 5px;">
                    <select id="add_new_child_gender" style="flex: 1; margin: 0;">
                        <option value="">選擇性別</option>
                        <option value="男">男</option>
                        <option value="女">女</option>
                    </select>
                    <input type="date" id="add_new_child_birthday" style="flex: 2; margin: 0;">
                </div>
                <small style="color: #888;">* 新增幼兒請務必填寫性別與生日</small>
            </div>
        </div>
        <button onclick="submitOldMemberCheckin()" style="background: #3c8326;">確認入館</button>
        <button onclick="location.reload()" class="btn-secondary">取消並返回</button>
    </div>

    <div id="result" class="container2 hidden">
        <div class="success-icon">✓</div>
        <h2 style="color: #28a745;">報到成功！</h2>
        <p id="welcome_user" style="font-size: 20px; font-weight: bold;"></p>
        <p style="color: #777; margin-top: 20px;">系統將於 05 秒後自動重置...</p>
    </div>

    <script>
        const zhongliVillages = ["三民里", "大崙里", "中央里", "中正里", "中建里", "中原里", "中堅里", "中榮里", "中壢里", "五福里", "五權里", "仁美里", "仁祥里", "仁義里", "仁德里", "仁福里", "內定里", "內厝里", "內壢里", "文化里", "水尾里", "正義里", "永光里", "永福里", "永興里", "石頭里", "光明里", "成功里", "自治里", "自立里", "自信里", "自強里", "至善里", "和平里", "幸福里", "忠孝里", "忠義里", "忠福里", "明德里", "東興里", "芝芭里", "青埔里", "信義里", "後寮里", "洽溪里", "振興里", "普仁里", "普忠里", "普強里", "普義里", "普慶里", "華勛里", "華愛里", "復興里", "復華里", "新街里", "新興里", "過嶺里", "福德里", "篤行里", "興平里", "興和里", "興南里", "興國里", "龍平里", "龍吉里", "龍安里", "龍岡里", "龍東里", "龍星里", "龍海里", "龍德里", "龍興里", "舊明里", "莊敬里", "健行里", "林森里", "德義里", "中山里", "中平里", "中興里"];
        const otherTaoyuanDistricts = ["桃園區", "平鎮區", "八德區", "楊梅區", "蘆竹區", "大溪區", "龍潭區", "龜山區", "大園區", "觀音區", "新屋區", "復興區"];

        function updateSubDistrict() {
            const region = document.getElementById('city_region').value;
            const subSelect = document.getElementById('sub_district');
            const otherInput = document.getElementById('other_city_input');

            subSelect.innerHTML = '<option value="">請選擇詳細區域</option>';
            subSelect.classList.add('hidden');
            otherInput.classList.add('hidden');

            if (region === '桃園市中壢區') {
                zhongliVillages.forEach(v => subSelect.innerHTML += `<option value="${v}">${v}</option>`);
                subSelect.classList.remove('hidden');
            } else if (region === '桃園市其他行政區') {
                otherTaoyuanDistricts.forEach(d => subSelect.innerHTML += `<option value="${d}">${d}</option>`);
                subSelect.classList.remove('hidden');
            } else if (region === '其他縣市') {
                otherInput.classList.remove('hidden');
            }
        }

        function generateChildFields(count) {
            const container = document.getElementById('dynamic_child_container');
            container.innerHTML = '';
            for (let i = 1; i <= count; i++) {
                const div = document.createElement('div');
                div.className = 'child-info-block';
                div.innerHTML = `
                    <span class="child-title">第 ${i} 位幼兒資料</span>
                    <input type="text" class="c_name" placeholder="幼兒姓名" required>
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

        let isRegistering = false;

        const form = document.getElementById('checkinForm');
        form.onsubmit = async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('submit_btn');
            submitBtn.disabled = true;

            // --- 新增：手動驗證區域是否填寫 ---
            let finalDistrict = "";
            if (isRegistering) {
                const region = document.getElementById('city_region').value;
                if (!region) {
                    alert("請選擇您來自哪個大區域");
                    submitBtn.disabled = false;
                    return;
                }

                if (region === '桃園市中壢區' || region === '桃園市其他行政區') {
                    const sub = document.getElementById('sub_district').value;
                    if (!sub) {
                        alert("請選擇詳細區域 (區或里)");
                        submitBtn.disabled = false;
                        return;
                    }
                    finalDistrict = (region === '桃園市中壢區' ? '桃園市中壢區 ' : '桃園市 ') + sub;
                } else if (region === '其他縣市') {
                    finalDistrict = document.getElementById('other_city_input').value || '其他縣市';
                }
            }

            const phone = document.getElementById('phone').value;
            const currentAction = isRegistering ? 'register' : 'check_member';

            const data = new URLSearchParams({
                respondent_type: document.querySelector('input[name="respondent_type"]:checked') ? document.querySelector('input[name="respondent_type"]:checked').value : '',
                action: currentAction,
                phone: phone,
                name: document.getElementById('parent_name') ? document.getElementById('parent_name').value : '',
                district: finalDistrict,
                purpose: document.getElementById('purpose') ? document.getElementById('purpose').value : '',
                relationship: document.getElementById('relationship') ? document.getElementById('relationship').value : '',
                adult_male: document.getElementById('adult_male') ? document.getElementById('adult_male').value : 0,
                adult_female: document.getElementById('adult_female') ? document.getElementById('adult_female').value : 0,
                child_count: document.getElementById('child_count') ? document.getElementById('child_count').value : 0,
                child_name: Array.from(document.querySelectorAll('.c_name')).map(el => el.value).join('|'),
                child_birthday: Array.from(document.querySelectorAll('.c_birthday')).map(el => el.value).join('|'),
                child_gender: Array.from(document.querySelectorAll('.c_gender')).map(el => el.value).join('|'),
                languages: Array.from(document.querySelectorAll('input[name="lang"]:checked')).map(el => el.value).join(',')
            });

            try {
                const response = await fetch('checkin_logic.php', {
                    method: 'POST',
                    body: data
                });
                const res = await response.json();

                if (res.status === 'need_register') {
                    document.getElementById('new_member_fields').classList.remove('hidden');
                    submitBtn.innerText = "完成註冊並報到";
                    isRegistering = true;
                } else if (res.status === 'old_member_select') {
                    document.getElementById('checkinArea').classList.add('hidden');
                    document.getElementById('old_member_selection').classList.remove('hidden');
                    window.currentMemberId = res.data.id;

                    const parentArea = document.getElementById('parent_checkboxes');
                    parentArea.innerHTML = '';
                    const parents = res.data.parent_name.split('|');
                    parents.forEach((p, index) => {
                        if (p.trim() !== "") {
                            parentArea.innerHTML += `
                            <div class="lang-item" id="p_wrap_${index}" style="justify-content: space-between;">
                                <label style="display: flex; align-items: center; cursor: pointer; flex-grow: 1;">
                                    <input type="checkbox" name="select_parent" value="${p}"> ${p}
                                </label>
                                <span onclick="deletePerson('parent', '${p}', 'p_wrap_${index}')" style="color: #dc3545; cursor: pointer; font-size: 14px; padding: 2px 8px; border: 1px solid #dc3545; border-radius: 4px;">刪除</span>
                            </div>`;
                        }
                    });

                    const childArea = document.getElementById('child_checkboxes');
                    childArea.innerHTML = '';
                    const children = res.data.child_name.split('|');
                    children.forEach((c, index) => {
                        if (c.trim() !== "") {
                            childArea.innerHTML += `
                            <div class="lang-item" id="c_wrap_${index}" style="justify-content: space-between;">
                                <label style="display: flex; align-items: center; cursor: pointer; flex-grow: 1;">
                                    <input type="checkbox" name="select_children" value="${c}"> ${c}
                                </label>
                                <span onclick="deletePerson('child', '${c}', 'c_wrap_${index}')" style="color: #dc3545; cursor: pointer; font-size: 14px; padding: 2px 8px; border: 1px solid #dc3545; border-radius: 4px;">刪除</span>
                            </div>`;
                        }
                    });
                } else if (res.status === 'success') {
                    showSuccess(res.user_name);
                } else {
                    alert(res.message || "處理失敗");
                }
            } catch (error) {
                alert("系統錯誤");
            } finally {
                submitBtn.disabled = false;
            }
        };

        async function deletePerson(type, name, elementId) {
            if (!confirm(`確定要從系統名單中永久刪除「${name}」嗎？`)) return;

            const data = new URLSearchParams({
                action: 'delete_person',
                member_id: window.currentMemberId,
                type: type,
                name: name
            });

            try {
                const response = await fetch('checkin_logic.php', {
                    method: 'POST',
                    body: data
                });
                const res = await response.json();
                if (res.status === 'success') {
                    document.getElementById(elementId).remove();
                } else {
                    alert('刪除失敗：' + (res.message || '未知錯誤'));
                }
            } catch (error) {
                alert('系統通訊錯誤');
            }
        }

        async function submitOldMemberCheckin() {
            const selectedParents = Array.from(document.querySelectorAll('input[name="select_parent"]:checked')).map(el => el.value).join('|');
            const selectedChildren = Array.from(document.querySelectorAll('input[name="select_children"]:checked')).map(el => el.value).join('|');

            const newParent = document.getElementById('add_new_parent').value;
            const newChild = document.getElementById('add_new_child').value;

            // 新增：抓取新幼兒的性別與生日
            const newChildGender = document.getElementById('add_new_child_gender').value;
            const newChildBirthday = document.getElementById('add_new_child_birthday').value;

            // 驗證：如果有填姓名，就必須填性別與生日
            if (newChild && (!newChildGender || !newChildBirthday)) {
                alert("請填寫新增幼兒的性別與生日");
                return;
            }

            if (!selectedParents && selectedChildren === "" && !newParent && !newChild) {
                alert("請至少勾選或新增一位入館人員");
                return;
            }

            const data = new URLSearchParams({
                action: 'final_checkin',
                member_id: window.currentMemberId,
                selected_parents: selectedParents,
                selected_children: selectedChildren,
                new_parent: newParent,
                new_child: newChild,
                // 新增參數傳給後端
                new_child_gender: newChildGender,
                new_child_birthday: newChildBirthday
            });

            try {
                const response = await fetch('checkin_logic.php', {
                    method: 'POST',
                    body: data
                });
                const res = await response.json();
                if (res.status === 'success') {
                    showSuccess(res.user_name);
                } else {
                    alert(res.message || "報到失敗");
                }
            } catch (e) {
                alert("通訊失敗，請檢查網路。");
            }
        }

        function showSuccess(name) {
            document.getElementById('checkinArea').classList.add('hidden');
            document.getElementById('old_member_selection').classList.add('hidden');
            document.getElementById('result').classList.remove('hidden');
            document.getElementById('welcome_user').innerText = `歡迎光臨，${name} 家長`;
            setTimeout(() => {
                location.reload();
            }, 5000);
        }
    </script>
</body>

</html>