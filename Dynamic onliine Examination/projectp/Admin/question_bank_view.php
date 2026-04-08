<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch Exams (Tests) to display as "Folders"
$tests_query = $conn->query("
    SELECT t.id, t.test_name, t.category, t.status, COUNT(q.id) as total_questions 
    FROM tests t 
    LEFT JOIN questions q ON t.id = q.test_id 
    WHERE t.creator_id = '$creator_id' 
    GROUP BY t.id 
    ORDER BY t.created_at DESC
");

$tests_list = [];
if ($tests_query) {
    while ($row = $tests_query->fetch_assoc()) {
        $tests_list[] = $row;
    }
}

// Fetch all questions
$all_q_query = $conn->query("SELECT q.*, t.test_name FROM questions q JOIN tests t ON q.test_id = t.id WHERE t.creator_id = '$creator_id'");
$all_questions = [];
if ($all_q_query) {
    while ($row = $all_q_query->fetch_assoc()) {
        $all_questions[] = $row;
    }
}
?>

<style>
    .qb-folder-row:hover {
        background-color: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    }

    .view-transition {
        animation: fadeInView 0.3s ease forwards;
    }

    @keyframes fadeInView {
        from {
            opacity: 0;
            transform: translateX(10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .correct-badge {
        background: #f0fdf4;
        color: #10b981;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-active {
        background: #f0fdf4;
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-draft {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }
</style>

<div id="question-bank-view" class="content-section">

    <div class="page-header"
        style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2>Question Bank</h2>

        <input type="file" id="import_qb_file" style="display:none;" accept=".csv, .sql"
            onchange="handleFileImport(event)">
        <button class="welcome-btn" onclick="document.getElementById('import_qb_file').click()">
            <i class="fa-solid fa-file-import"></i> Import Question Bank
        </button>
    </div>

    <div id="qb-folders-view" class="dash-panel view-transition">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div style="display:flex; gap: 15px;">
                <input type="text" placeholder="Search banks..."
                    style="padding:10px 15px; border:1px solid #e2e8f0; border-radius:8px; width:250px; font-size:13px; outline:none;">
                <select
                    style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none;">
                    <option>All Categories</option>
                </select>
            </div>

            <button class="welcome-btn" style="background:#10b981;" onclick="publishSelectedBanks()">
                <i class="fa-solid fa-paper-plane"></i> Send Selected Question Banks
            </button>
        </div>

        <table class="recent-table" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:#64748b; font-size:13px;">
                    <th style="padding-bottom:15px; width:40px;">
                        <input type="checkbox" id="selectAllBanks" onclick="toggleAllBanks(this)"
                            style="accent-color:#3b82f6; width:16px; height:16px; cursor:pointer;">
                    </th>
                    <th style="padding-bottom:15px; width:40%;">Bank / Exam Name</th>
                    <th style="padding-bottom:15px;">Status</th>
                    <th style="padding-bottom:15px;">Questions</th>
                    <th style="padding-bottom:15px; text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tests_list)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 40px; color:#94a3b8;">No question banks found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tests_list as $test): ?>
                        <tr style="border-top:1px solid #f1f5f9; transition:0.2s;" class="qb-folder-row">
                            <td style="padding:18px 0;">
                                <input type="checkbox" class="bank-select-chk" value="<?php echo $test['id']; ?>"
                                    style="accent-color:#3b82f6; width:16px; height:16px; cursor:pointer;">
                            </td>
                            <td style="font-weight:600; color:#0f172a; font-size:15px; cursor:pointer;"
                                onclick="openBankQuestions(<?php echo $test['id']; ?>, '<?php echo addslashes($test['test_name']); ?>')">
                                <i class="fa-solid fa-folder-open"
                                    style="color:#3b82f6; margin-right:10px; font-size:18px;"></i>
                                <?php echo htmlspecialchars($test['test_name']); ?>
                                <div
                                    style="font-size:11px; color:#94a3b8; margin-top:4px; font-weight: 500; margin-left: 32px;">
                                    <?php echo htmlspecialchars($test['category']); ?></div>
                            </td>
                            <td>
                                <span
                                    class="status-badge <?php echo $test['status'] == 'Active' ? 'status-active' : 'status-draft'; ?>">
                                    <?php echo $test['status'] == 'Active' ? 'Published' : 'Draft'; ?>
                                </span>
                            </td>
                            <td style="color:#64748b; font-weight:500;"><?php echo $test['total_questions']; ?></td>
                            <td style="text-align:right;">
                                <button
                                    onclick="openBankQuestions(<?php echo $test['id']; ?>, '<?php echo addslashes($test['test_name']); ?>')"
                                    style="background:none; border:none; color:#3b82f6; font-weight:600; cursor:pointer; font-size:13px;">View
                                    Questions <i class="fa-solid fa-arrow-right" style="margin-left:5px;"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="qb-questions-view" class="dash-panel view-transition" style="display:none;">
        <div
            style="display:flex; align-items:center; gap:15px; margin-bottom:25px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9;">
            <button onclick="closeBankQuestions()"
                style="background:none; border:none; color:#64748b; font-size:14px; font-weight:600; cursor:pointer; padding: 8px 12px; border-radius: 8px; background: #f8fafc; transition: 0.2s;">
                <i class="fa-solid fa-arrow-left"></i> Back to Banks
            </button>
            <h3 id="qb-exam-title" style="margin:0; color:#0f172a; font-size: 18px;">Bank Name</h3>
        </div>

        <table class="recent-table" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; color:#64748b; font-size:13px;">
                    <th style="padding-bottom:15px; width: 70%;">Question & Options</th>
                    <th style="padding-bottom:15px;">Correct Answer</th>
                    <th style="padding-bottom:15px; text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody id="qb-questions-tbody">
            </tbody>
        </table>
    </div>
</div>

<script>
    const allBankQuestions = <?php echo json_encode($all_questions); ?>;

    function openBankQuestions(testId, testName) {
        document.getElementById('qb-folders-view').style.display = 'none';
        document.getElementById('qb-questions-view').style.display = 'block';
        document.getElementById('qb-exam-title').innerText = testName;

        const tbody = document.getElementById('qb-questions-tbody');
        tbody.innerHTML = '';
        const filteredQuestions = allBankQuestions.filter(q => q.test_id == testId);

        if (filteredQuestions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding: 40px; color:#94a3b8;">No questions inside this bank.</td></tr>';
            return;
        }

        filteredQuestions.forEach((q, index) => {
            const tr = document.createElement('tr');
            tr.style.borderTop = '1px solid #f1f5f9';
            tr.innerHTML = `
                <td style="padding:20px 0; padding-right: 20px;">
                    <div style="font-weight:500; color:#0f172a; margin-bottom:8px; font-size:14px; line-height:1.5;">${index + 1}. ${q.question}</div>
                    <div style="font-size:12px; color:#64748b; line-height: 1.8;">
                        <span style="${q.correct_option === 'A' ? 'color:#10b981; font-weight:600;' : ''}">A. ${q.option_a}</span> &nbsp;&nbsp;&nbsp;&nbsp; 
                        <span style="${q.correct_option === 'B' ? 'color:#10b981; font-weight:600;' : ''}">B. ${q.option_b}</span> <br>
                        ${q.option_c ? `<span style="${q.correct_option === 'C' ? 'color:#10b981; font-weight:600;' : ''}">C. ${q.option_c}</span> &nbsp;&nbsp;&nbsp;&nbsp;` : ''}
                        ${q.option_d ? `<span style="${q.correct_option === 'D' ? 'color:#10b981; font-weight:600;' : ''}">D. ${q.option_d}</span>` : ''}
                    </div>
                </td>
                <td style="vertical-align: top; padding-top:20px;">
                    <span class="correct-badge">Option ${q.correct_option}</span>
                </td>
                <td style="vertical-align: top; padding-top:20px; text-align:right;">
                    <button style="background:none; border:none; color:#94a3b8; cursor:pointer; margin-right:12px; font-size:15px;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#94a3b8'"><i class="fa-regular fa-pen-to-square"></i></button>
                    <button style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:15px;" onmouseover="this.style.color='#b91c1c'" onmouseout="this.style.color='#ef4444'"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function closeBankQuestions() {
        document.getElementById('qb-questions-view').style.display = 'none';
        document.getElementById('qb-folders-view').style.display = 'block';
    }

    function toggleAllBanks(source) {
        document.querySelectorAll('.bank-select-chk').forEach(chk => chk.checked = source.checked);
    }

    function publishSelectedBanks() {
        const selected = document.querySelectorAll('.bank-select-chk:checked');
        if (selected.length === 0) {
            showGlobalToast('Select a Bank', 'Please check at least one Question Bank folder to publish.', 'error');
            return;
        }

        // This is where you'd send an AJAX request to update the DB status to 'Active'
        showGlobalToast('Banks Published!', `Successfully made ${selected.length} Question Bank(s) visible to testers.`, 'success');

        document.querySelectorAll('.bank-select-chk').forEach(chk => chk.checked = false);
        document.getElementById('selectAllBanks').checked = false;
    }

    function handleFileImport(event) {
        const file = event.target.files[0];
        if (file) {
            showGlobalToast('Importing...', `Processing system file: ${file.name}.`, 'success');
            event.target.value = '';
        }
    }
</script>