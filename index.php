<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>中壢過嶺親子館 | 115年度入館登記</title>
    <style>
        body { font-family: sans-serif; padding: 20px; text-align: center; background-color: #f4f7f6; color: #333; }
        .container { max-width: 450px; margin: auto; padding: 25px; border-radius: 20px; background: white; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        h2 { color: #e2ad39; margin-bottom: 5px; }
        h3 { font-size: 14px; color: #777; margin-top: 0; }
        input, select { width: 95%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; box-sizing: border-box; }
        .label-group { text-align: left; margin: 15px 5% 5px 5%; font-weight: bold; color: #555; border-left: 4px solid #e2ad39; padding-left: 8px; }
        .flex-row { display: flex; align-items: center; justify-content: space-between; margin: 5px 5%; }
        button { width: 96%; padding: 15px; background: #e2ad39; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 17px; font-weight: bold; margin-top: 20px; }
        .hidden { display: none; }
        .lang-grid { display: grid; grid-template-columns: 1fr 1fr; text-align: left; margin-left: 10%; font-size: 14px; }
        .success-icon { font-size: 60px; color: #28a745; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
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
                    <option value="桃園其他行政區">桃園其他行政區 Other Dist</option>
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
                <div class="flex-row"><span>男性 / Male:</span><input type="number" id="adult_male" value="0" min="0" style="width: 60px;"></div>
                <div class="flex-row"><span>女性 / Female:</span><input type="number" id="adult_female" value="0" min="0" style="width: 60px;"></div>

                <div class="label-group">幼兒的人數</div>
                <input type="number" id="child_count" value="0" min="0">

                <div class="label-group">填答者身分別 (選填)</div>
                <select id="respondent_type">
                    <option value="">無</option>
                    <option value="原住民">原住民</option>
                    <option value="新住民">新住民</option>
                </select>

                <div class="label-group">常用語言 (可多選)</div>
                <div class="lang-grid">
                    <label><input type="checkbox" name="lang" value="國語"> 國語</label>
                    <label><input type="checkbox" name="lang" value="台語"> 台語</label>
                    <label><input type="checkbox" name="lang" value="客語"> 客語</label>
                    <label><input type="checkbox" name="lang" value="英語"> 英語</label>
                    <label><input type="checkbox" name="lang" value="越南語"> 越南語</label>
                    <label><input type="checkbox" name="lang" value="印尼語"> 印尼語</label>
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
        const form = document.getElementById('checkinForm');
        form.onsubmit = async (e) => {
            e.preventDefault();
            const phone = document.getElementById('phone').value;
            const langs = Array.from(document.querySelectorAll('input[name="lang"]:checked')).map(el => el.value).join(',');

            const data = new URLSearchParams({
                phone: phone,
                name: document.getElementById('parent_name').value,
                district: document.getElementById('district').value,
                purpose: document.getElementById('purpose').value,
                relationship: document.getElementById('relationship').value,
                adult_male: document.getElementById('adult_male').value,
                adult_female: document.getElementById('adult_female').value,
                child_count: document.getElementById('child_count').value,
                respondent_type: document.getElementById('respondent_type').value,
                languages: langs
            });

            const response = await fetch('checkin_logic.php', { method: 'POST', body: data });
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
            } else { alert(res.message); }
        };
    </script>
</body>
</html>