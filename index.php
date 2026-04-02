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
            font-size: 18px;
        }

        .hidden {
            display: none;
        }

        .lang-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            text-align: left;
            margin: 10px 8%;
            font-size: 16px;
        }

        .lang-item {
            display: flex;
            align-items: center;
            font-size: 20px;
            cursor: pointer;
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
    </style>
</head>

<body>
    <div class="container1">
        <div class="box01" style="background-image: url(./img/親子館.jpg); 
        height: 500px;
        background-size: cover; background-position: center;"></div>
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
                    <option value="桃園市平鎮區">桃園市平鎮區 Pingzhen Dist</option>
                    <option value="桃園市八德區">桃園市八德區 Bade Dist</option>
                    <option value="桃園楊梅區">桃園楊梅區 Yangmei Dist</option>
                    <option value="桃園市蘆竹區">桃園市蘆竹區 Luzhu Dist</option>
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

                <div class="label-group">常用語言 (可多選)</div>
                <div class="lang-grid">
                    <label class="lang-item"><input type="checkbox" name="lang" value="國語"> 國語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="台語"> 台語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="客語"> 客語</label>
                    <label class="lang-item"><input type="checkbox" name="lang" value="英語"> 英語</label>
                </div>
            </div>
            <button type="submit" id="submit_btn">確認報到</button>
        </form>
    </div>

    <div id="old_member_selection" class="container2 hidden">
        <h2>歡迎回來</h2>
        <p style="color: #666; margin-bottom: 20px;">請勾選今日入館人員，或在下方新增：</p>
        
        <div class="label-group">家長名單</div>
        <div id="parent_list" class="lang-grid" style="grid-template-columns: 1fr;"></div>
        <input type="text" id="add_new_parent" placeholder="+ 新增其他同行家長姓名" style="margin-top: 5px; border: 1px dashed #48a187;">
        
        <div class="label-group">幼兒名單</div>
        <div id="child_list" class="lang-grid" style="grid-template-columns: 1fr;"></div>
        <input type="text" id="add_new_child" placeholder="+ 新增其他同行幼兒姓名" style="margin-top: 5px; border: 1px dashed #48a187;">

        <button type="button" onclick="submitOldMemberCheckin()">確認並報到</button>
        <button type="button" class="btn-secondary" onclick="location.reload()">返回</button>
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
                    <div style="text-align:left; font-size:13px; color:#666; margin-left:2%;">出生日期：</div>
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
            submitBtn.innerText = "處理中...";

            const phone = document.getElementById('phone').value;
            const parentName = document.getElementById('parent_name').value;
            
            const data = new URLSearchParams({
                phone: phone,
                name: parentName,
                district: document.getElementById('district').value,
                purpose: document.getElementById('purpose').value,
                relationship: document.getElementById('relationship').value,
                adult_male: document.getElementById('adult_male').value,
                adult_female: document.getElementById('adult_female').value,
                child_count: document.getElementById('child_count').value,
                child_name: Array.from(document.querySelectorAll('.c_name')).map(el => el.value).join('|'),
                child_birthday: Array.from(document.querySelectorAll('.c_birthday')).map(el => el.value).join('|'),
                child_gender: Array.from(document.querySelectorAll('.c_gender')).map(el => el.value).join('|'),
                languages: Array.from(document.querySelectorAll('input[name="lang"]:checked')).map(el => el.value).join(',')
            });

            try {
                const response = await fetch('checkin_logic.php', { method: 'POST', body: data });
                const responseText = await response.text();
                console.log("PHP 回傳內容：", responseText); 
                const res = JSON.parse(responseText);

                if (res.status === 'need_register') {
                    document.getElementById('new_member_fields').classList.remove('hidden');
                    document.getElementById('parent_name').required = true;
                    submitBtn.innerText = "完成註冊並報到";
                } else if (res.status === 'old_member_select') {
                    document.getElementById('checkinArea').classList.add('hidden');
                    const selectArea = document.getElementById('old_member_selection');
                    selectArea.classList.remove('hidden');
                    
                    document.getElementById('parent_list').innerHTML = `
                        <label class="lang-item"><input type="checkbox" name="select_parent" value="${res.data.parent_name}" checked> ${res.data.parent_name} (家長)</label>
                    `;
                    
                    const children = res.data.child_name.split('|');
                    let childHtml = '';
                    children.forEach(name => {
                        childHtml += `<label class="lang-item"><input type="checkbox" name="select_children" value="${name}" checked> ${name}</label>`;
                    });
                    document.getElementById('child_list').innerHTML = childHtml;
                    
                    window.currentMemberId = res.data.id;
                } else if (res.status === 'success') {
                    showSuccess(res.user_name);
                } else {
                    alert(res.message || "發生未知錯誤");
                }
            } catch (error) {
                console.error("解析失敗：", error);
                alert("系統錯誤，請查看 Console。");
            } finally {
                submitBtn.disabled = false;
            }
        };

        // 提交舊會員勾選與新增結果
        async function submitOldMemberCheckin() {
            const selectedChildren = Array.from(document.querySelectorAll('input[name="select_children"]:checked')).map(el => el.value).join('|');
            const selectedParents = Array.from(document.querySelectorAll('input[name="select_parent"]:checked')).map(el => el.value).join('|');
            
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