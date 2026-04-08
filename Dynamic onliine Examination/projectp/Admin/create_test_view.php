<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<style>
    /* Builder Sliding Steps */
    .builder-step {
        display: none;
        animation: slideIn 0.4s ease forwards;
        max-width: 900px;
        margin: 0 auto;
    }

    .builder-step.active {
        display: block;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: 0.3s;
        font-family: 'Poppins', sans-serif;
        background: #f8fafc;
    }

    .form-input:focus {
        border-color: #3b82f6;
        background: white;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Google Forms Style Question Card */
    .gform-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
        border-left: 6px solid transparent;
        transition: 0.3s;
        position: relative;
    }

    .gform-card.active-card {
        border-left-color: #3b82f6;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    .q-title-input {
        font-size: 16px;
        width: 100%;
        border: none;
        background: #f1f5f9;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        outline: none;
        resize: none;
        font-family: 'Poppins', sans-serif;
        transition: 0.3s;
    }

    .q-title-input:focus {
        background: #e2e8f0;
    }

    .opt-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .opt-input {
        flex: 1;
        border: none;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px 5px;
        font-size: 14px;
        outline: none;
        transition: 0.3s;
    }

    .opt-input:focus {
        border-bottom-color: #3b82f6;
    }

    /* Add Menu (From your Screenshot) */
    .menu-split {
        display: flex;
        gap: 30px;
        margin-top: 30px;
        margin-bottom: 60px;
    }

    .menu-col {
        flex: 1;
    }

    .menu-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .icon-circle.blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .icon-circle.green {
        background: #f0fdf4;
        color: #10b981;
    }

    .menu-header h4 {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .menu-header p {
        font-size: 13px;
        color: #64748b;
        margin: 2px 0 0 0;
    }

    .action-btn {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        border: 1px solid #e2e8f0;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: 0.2s;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
    }

    .action-btn:hover {
        border-color: #cbd5e1;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }

    .action-btn-left {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 15px;
        font-weight: 500;
        color: #0f172a;
    }

    .action-btn-left i {
        font-size: 18px;
    }

    .action-btn i.fa-chevron-right {
        color: #94a3b8;
        font-size: 14px;
    }

    /* Custom Modal Styles */
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100000;
        opacity: 0;
        animation: fadeInModal 0.3s forwards;
    }

    @keyframes fadeInModal {
        to {
            opacity: 1;
        }
    }

    .custom-modal {
        background: white;
        width: 100%;
        max-width: 420px;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        transform: translateY(20px);
        animation: slideUpModal 0.3s forwards;
        overflow: hidden;
    }

    @keyframes slideUpModal {
        to {
            transform: translateY(0);
        }
    }

    .custom-modal-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .custom-modal-header h3 {
        margin: 0;
        font-size: 16px;
        color: #0f172a;
    }

    .custom-modal-header button {
        background: none;
        border: none;
        font-size: 18px;
        color: #94a3b8;
        cursor: pointer;
        transition: 0.2s;
    }

    .custom-modal-header button:hover {
        color: #ef4444;
    }

    .custom-modal-body {
        padding: 25px;
    }

    .custom-modal-body label {
        font-size: 14px;
        color: #475569;
        line-height: 1.5;
        display: block;
        font-weight: 500;
    }

    .custom-modal-footer {
        padding: 15px 25px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .cancel-btn {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        color: #475569;
        cursor: pointer;
        transition: 0.2s;
    }

    .cancel-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }
</style>

<div id="create-test-view" class="content-section">

    <div id="createStep1" class="builder-step active">
        <div style="text-align:center; padding:40px 20px;">
            <i class="fa-solid fa-laptop-code" style="font-size:60px; color:#3b82f6; margin-bottom:20px;"></i>
            <h2 style="font-size:28px; color:#0f172a; margin-bottom:10px;">Create a New Exam</h2>
            <p style="color:#64748b;">Follow these simple steps to construct and deploy your professional assessment.
            </p>

            <div style="display:flex; justify-content:center; gap:25px; margin:40px 0; text-align:left;">
                <div class="dash-panel" style="flex:1; max-width:260px; border-top:4px solid #3b82f6; padding: 25px;">
                    <h4 style="margin: 0;">1. Details</h4>
                    <p style="font-size:13px; color:#64748b; margin-top:8px;">Define the exam title, passing criteria,
                        and time limits.</p>
                </div>
                <div class="dash-panel" style="flex:1; max-width:260px; border-top:4px solid #f97316; padding: 25px;">
                    <h4 style="margin: 0;">2. Questions</h4>
                    <p style="font-size:13px; color:#64748b; margin-top:8px;">Use the smart builder to add your
                        questions and options.</p>
                </div>
                <div class="dash-panel" style="flex:1; max-width:260px; border-top:4px solid #10b981; padding: 25px;">
                    <h4 style="margin: 0;">3. Publish</h4>
                    <p style="font-size:13px; color:#64748b; margin-top:8px;">Set visibility and distribute the exam
                        link to attendees.</p>
                </div>
            </div>

            <button class="welcome-btn" style="margin: 0 auto; padding:14px 40px; font-size:16px;"
                onclick="goToCreateStep(2)">
                Get Started <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </div>
    </div>

    <div id="createStep2" class="builder-step">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
            <h2 style="color:#0f172a; margin: 0;">Exam Configuration</h2>
            <button onclick="goToCreateStep(1)"
                style="background:none; border:none; color:#64748b; cursor:pointer; font-weight:600; font-size: 14px;"><i
                    class="fa-solid fa-arrow-left"></i> Back</button>
        </div>

        <div class="dash-panel">
            <label style="font-size:13px; font-weight:600; color:#334155;">Exam Title *</label>
            <input type="text" id="ex_title" class="form-input" placeholder="e.g. Midterm JavaScript Assessment">

            <label style="font-size:13px; font-weight:600; color:#334155; margin-top:15px; display:block;">Instructions
                for Students</label>
            <textarea id="ex_desc" class="form-input" rows="3" placeholder="Explain the rules..."></textarea>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                <div>
                    <label style="font-size:13px; font-weight:600; color:#334155;">Duration (Minutes)</label>
                    <input type="number" id="ex_duration" class="form-input" value="30" placeholder="30" min="1">
                </div>
                <div>
                    <label style="font-size:13px; font-weight:600; color:#334155;">Total Marks</label>
                    <input type="number" id="ex_total" class="form-input" value="100" placeholder="100" min="1">
                </div>
                <div>
                    <label style="font-size:13px; font-weight:600; color:#334155;">Pass Marks</label>
                    <input type="number" id="ex_pass" class="form-input" value="40" placeholder="40" min="1">
                </div>
                <div>
                    <label style="font-size:13px; font-weight:600; color:#334155;">Category</label>
                    <input type="text" id="ex_category" class="form-input" placeholder="e.g. Programming">
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; margin-top:20px;">
            <button class="welcome-btn" onclick="goToCreateStep(3)">Next: Add Questions <i
                    class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></button>
        </div>
    </div>

    <div id="createStep3" class="builder-step">
        <div
            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; background:white; padding:15px 25px; border-radius:12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
            <div style="display:flex; align-items:center; gap:15px;">
                <button onclick="goToCreateStep(2)"
                    style="background:none; border:none; color:#64748b; cursor:pointer;"><i
                        class="fa-solid fa-arrow-left"></i></button>
                <h3 id="display_ex_title" style="margin:0; font-size:18px; color: #0f172a;">Untitled Exam</h3>
            </div>

            <div style="display:flex; align-items:center; gap:15px;">
                <select id="ex_status"
                    style="padding:8px 15px; border-radius:8px; border:1px solid #e2e8f0; outline:none; background:#f8fafc; font-weight:500; color: #0f172a;">
                    <option value="Active">🌍 Public (Live)</option>
                    <option value="Draft">🔒 Private (Draft)</option>
                </select>
                <button class="welcome-btn" style="background:#10b981;" onclick="publishExam(this)">
                    <i class="fa-solid fa-paper-plane"></i> Publish Exam
                </button>
            </div>
        </div>

        <div id="qBuilderContainer"></div>

        <div class="menu-split">
            <div class="menu-col">
                <div class="menu-header">
                    <div class="icon-circle blue"><i class="fa-solid fa-bars"></i></div>
                    <div>
                        <h4>Fixed Questions</h4>
                        <p>Test takers get the same set of questions.</p>
                    </div>
                </div>

                <div class="action-btn" onclick="appendQuestionBlock()">
                    <div class="action-btn-left"><i class="fa-regular fa-circle-plus"></i> Add a new question</div>
                    <i class="fa-solid fa-chevron-right"></i>
                </div>

                <div class="action-btn"
                    onclick="showGlobalToast('Feature In Progress', 'Selecting from the Question Bank directly is coming in the next update!', 'success')">
                    <div class="action-btn-left"><i class="fa-solid fa-layer-group"></i> Select from Question Bank</div>
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
                <div class="action-btn"
                    onclick="showGlobalToast('Feature In Progress', 'CSV Importing is coming in the next update!', 'success')">
                    <div class="action-btn-left"><i class="fa-solid fa-file-csv"></i> Import questions (.CSV)</div>
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>

            <div class="menu-col" style="border-left: 1px solid #e2e8f0; padding-left: 30px;">
                <div class="menu-header">
                    <div class="icon-circle green"><i class="fa-solid fa-shuffle"></i></div>
                    <div>
                        <h4>Random Questions</h4>
                        <p>Test takers get randomly selected questions.</p>
                    </div>
                </div>

                <div class="action-btn" onclick="openRandomModal()">
                    <div class="action-btn-left"><i class="fa-solid fa-shuffle"></i> Set up random questions</div>
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
        </div>
    </div>

    <div id="randomQuestionsModal" class="custom-modal-overlay" style="display:none;">
        <div class="custom-modal">
            <div class="custom-modal-header">
                <h3>Import Random Questions</h3>
                <button type="button" onclick="closeRandomModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="custom-modal-body">
                <label for="randomCountInput">How many random questions do you want to pull from your Question
                    Bank?</label>
                <input type="number" id="randomCountInput" class="form-input" min="1" value="5"
                    style="margin-top:15px; font-size: 16px; padding: 12px;">
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="cancel-btn" onclick="closeRandomModal()">Cancel</button>
                <button type="button" class="welcome-btn" onclick="confirmRandomImport()"><i
                        class="fa-solid fa-download"></i> Import</button>
            </div>
        </div>
    </div>

</div>