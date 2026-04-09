<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>中壢過嶺親子館 | 115年度入館登記</title>
    <style>
        body { font-family: sans-serif; padding: 20px; text-align: center; background-color: #f4f7f6; color: #333; }
        .container1 { max-width: 1000px; max-height: 500px; margin: auto; padding: 25px; }
        .container2 { max-width: 900px; margin: auto; padding: 25px; border-radius: 20px; background: white; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); }
        h2 { color: #2c9635; margin-bottom: 5px; font-size: 34px; }
        h3 { font-size: 20px; color: #777; margin-top: 0; }
        input, select { width: 95%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 8px; font-size: 20px; box-sizing: border-box; }
        .label-group { text-align: left; margin: 15px 5% 5px 5%; font-weight: bold; font-size: 20px; color: #555; border-left: 4px solid #468b4c; padding-left: 8px; margin-top: 20px; }
        .flex-row { display: flex; align-items: center; justify-content: space-between; margin: 5px 5%; font-size: 18px; }
        button { width: 96%; padding: 15px; background: #48a187; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 25px; font-weight: bold; margin-top: 20px; }
        .btn-secondary { background: #6c757d; margin-top: 10px; font-size: 25px; }
        .hidden { display: none; }
        .lang-item { display: flex; align-items: center; font-size: 20px; padding: 10px 0; }
        .lang-item input { width: 30px; height: 30px; margin-right: 15px; }
        .child-info-block { background: #f9f9f9; padding: 15px; border-radius: 10px; margin: 10px 5%; border: 1px dashed #57c05f; text-align: left; }
        .success-icon { font-size: 60px; color: #28a745; margin-bottom: 10px; }
    </style>
</head>

<body>
    <div class="container1">
        <div class="box01" style="background-image: url(./img/親子館.jpg); height: 500px; background-size: cover; background-position: center;"></div>
    </div>

    <div class="container2" id="checkinArea">
        <h2>中壢過嶺親子館</h2>
        <h3 id="form_title">115年度入館登記</h3>

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
                    <option value="其他縣市">其他縣市 Other City</option>
                </select>
                <div class="label-group">大人人數 (含填答者)</div>
                <div class="flex-row"><span>男性 / Male:</span><input type="number" id="adult_male" value="0" min="0" style="width: 80px;"></div>
                <div class="flex-row"><span>女性 / Female:</span><input type="number" id="adult_female" value="0" min="0" style="width: 80px;"></div>
                <div class="label-group">幼兒人數與資訊</div>
                <div class="flex-row"><span>入館幼兒總數:</span><input type="number" id="child_count" value="0" min="0" max="5" style="width: 80px;" onchange="generateChildFields(this.value)"></div>
                <div id="dynamic_child_container"></div>
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
            <input type="text" id="add_new_child" placeholder="新增其他幼兒姓名" style="width: 95%;">
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

        const form = document.getElementById('checkinForm');
        form.onsubmit = async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('submit_btn');
            submitBtn.disabled = true;

            const phone = document.getElementById('phone').value;
            const newMemberFields = document.getElementById('new_member_fields');
            let data;

            // 判斷目前是否為「註冊模式」（隱藏的註冊區塊已被展開）
            if (!newMemberFields.classList.contains('hidden')) {
                const cNames = Array.from(document.querySelectorAll('.c_name')).map(el => el.value).join('|');
                const cBirthdays = Array.from(document.querySelectorAll('.c_birthday')).map(el => el.value).join('|');
                const cGenders = Array.from(document.querySelectorAll('.c_gender')).map(el => el.value).join('|');

                // 構建傳送給後端「功能 D：新會員註冊流程」的正確參數
                data = new URLSearchParams({
                    action: 'register', // 👉 補上這行專屬指令
                    name: document.getElementById('parent_name').value, 
                    phone: phone,
                    district: document.getElementById('district').value,
                    adult_male: document.getElementById('adult_male').value,
                    adult_female: document.getElementById('adult_female').value,
                    child_count: document.getElementById('child_count').value,
                    child_name: cNames,
                    child_birthday: cBirthdays,
                    child_gender: cGenders
                });
            } else {
                // 單純查詢舊會員模式
                data = new URLSearchParams({
                    phone: phone,
                    action: 'check_member'
                });
            }

            try {
                const response = await fetch('checkin_logic.php', { method: 'POST', body: data });
                const res = await response.json();

                if (res.status === 'need_register') {
                    document.getElementById('new_member_fields').classList.remove('hidden');
                    submitBtn.innerText = "完成註冊並報到";
                } else if (res.status === 'old_member_select') {
                    document.getElementById('checkinArea').classList.add('hidden');
                    document.getElementById('old_member_selection').classList.remove('hidden');
                    window.currentMemberId = res.data.id;

                    // 重新生成家長 Checkboxes (包含刪除按鈕)
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

                    // 重新生成幼兒 Checkboxes (包含刪除按鈕)
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
                }
            } catch (error) {
                alert("系統錯誤");
            } finally {
                submitBtn.disabled = false;
            }
        };

        // 新增的刪除函式
        async function deletePerson(type, name, elementId) {
            if (!confirm(`確定要從系統名單中永久刪除「${name}」嗎？`)) return;

            const data = new URLSearchParams({
                action: 'delete_person',
                member_id: window.currentMemberId,
                type: type,
                name: name
            });

            try {
                const response = await fetch('checkin_logic.php', { method: 'POST', body: data });
                const res = await response.json();
                if (res.status === 'success') {
                    // 刪除成功後，從畫面上直接移除該欄位
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
                new_child: newChild
            });

            try {
                const response = await fetch('checkin_logic.php', { method: 'POST', body: data });
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
            setTimeout(() => { location.reload(); }, 5000);
        }
    </script>
</body>

</html>