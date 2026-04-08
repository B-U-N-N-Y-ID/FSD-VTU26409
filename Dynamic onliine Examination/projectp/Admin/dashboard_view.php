<div id="dashboard-view" class="content-section active">
    
    <div class="welcome-banner">
        <div class="welcome-text">
            <h2><?php echo $greet . ", " . htmlspecialchars($_SESSION['first_name']); ?>! 👋</h2>
            <p>Here is what's happening with your exams and students today.</p>
        </div>
        <button class="welcome-btn" onclick="document.querySelector('[data-target=\'create-test-view\']').click();"><i class="fa-solid fa-plus"></i> Create New Test</button>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div class="stat-info">
                    <h4>Tests Created</h4>
                    <p><?php echo $tests_count; ?></p>
                    <?php if ($tests_this_week > 0): ?>
                        <div class="trend up"><i class="fa-solid fa-arrow-trend-up"></i> +<?php echo $tests_this_week; ?> this week</div>
                    <?php else: ?>
                        <div class="trend neutral"><i class="fa-solid fa-arrow-right"></i> No new tests this week</div>
                    <?php endif; ?>
                </div>
                <div class="stat-icon blue"><i class="fa-solid fa-clipboard-check"></i></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div class="stat-info">
                    <h4>Total Attendees</h4>
                    <p><?php echo $attendees_count; ?></p>
                    <?php if ($attendees_this_month > 0): ?>
                        <div class="trend up"><i class="fa-solid fa-arrow-trend-up"></i> +<?php echo $attendees_this_month; ?> this month</div>
                    <?php else: ?>
                        <div class="trend neutral"><i class="fa-solid fa-arrow-right"></i> No new attendees</div>
                    <?php endif; ?>
                </div>
                <div class="stat-icon orange"><i class="fa-solid fa-user-graduate"></i></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div class="stat-info">
                    <h4>Avg. Pass Rate</h4>
                    <p><?php echo $pass_rate; ?>%</p>
                    <?php if ($pass_rate_diff > 0): ?>
                        <div class="trend up"><i class="fa-solid fa-arrow-trend-up"></i> +<?php echo $pass_rate_diff; ?>% recently</div>
                    <?php elseif ($pass_rate_diff < 0): ?>
                        <div class="trend down"><i class="fa-solid fa-arrow-trend-down"></i> <?php echo $pass_rate_diff; ?>% recently</div>
                    <?php else: ?>
                        <div class="trend neutral"><i class="fa-solid fa-arrow-right"></i> Stable pass rate</div>
                    <?php endif; ?>
                </div>
                <div class="stat-icon green"><i class="fa-solid fa-chart-pie"></i></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div class="stat-info">
                    <h4>Certificates Issued</h4>
                    <p><?php echo $certs_count; ?></p>
                    <?php if ($certs_today > 0): ?>
                        <div class="trend up"><i class="fa-solid fa-arrow-trend-up"></i> +<?php echo $certs_today; ?> new today</div>
                    <?php else: ?>
                        <div class="trend neutral"><i class="fa-solid fa-arrow-right"></i> None issued today</div>
                    <?php endif; ?>
                </div>
                <div class="stat-icon purple"><i class="fa-solid fa-award"></i></div>
            </div>
        </div>
    </div>

    <div class="dashboard-columns">
        <div class="dash-panel">
            <div class="panel-header">
                <h3>Recent Tests</h3>
                <a href="#" onclick="document.querySelector('[data-target=\'all-tests-view\']').click();">View All <i class="fa-solid fa-arrow-right" style="margin-left:4px; font-size:11px;"></i></a>
            </div>
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>Test Name</th>
                        <th>Date Created</th>
                        <th>Attendees</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_tests)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 40px; color: #94a3b8;">
                                <i class="fa-solid fa-folder-open" style="font-size:32px; margin-bottom:10px; color:#cbd5e1;"></i><br>
                                No tests created yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_tests as $test): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;"><?php echo htmlspecialchars($test['test_name']); ?></div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top:2px;"><?php echo htmlspecialchars($test['category']); ?></div>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($test['created_at'])); ?></td>
                                <td><?php echo $test['attendee_count'] > 0 ? $test['attendee_count'] : '-'; ?></td>
                                <td>
                                    <?php if ($test['status'] == 'Active'): ?>
                                        <span class="status active">Active</span>
                                    <?php else: ?>
                                        <span class="status draft">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td><i class="fa-solid fa-ellipsis action-dot"></i></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="dash-panel">
            <div class="panel-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="setup-step">
                <div class="step-icon"><i class="fa-solid fa-plus"></i></div>
                <div class="step-info">
                    <h4>Create New Test</h4>
                    <p>Start from scratch or import from your bank.</p>
                    <button class="btn-sm" onclick="document.querySelector('[data-target=\'create-test-view\']').click();">Create Test</button>
                </div>
            </div>
            <hr style="border:none; border-top:1px solid #f1f5f9; margin: 20px 0;">
            <div class="setup-step">
                <div class="step-icon" style="background: #f0fdf4; color: #10b981;"><i class="fa-solid fa-share-nodes"></i></div>
                <div class="step-info">
                    <h4>Invite Students</h4>
                    <p>Send secure exam links to attendees.</p>
                    <button class="btn-sm" style="background: #10b981;">Send Invites</button>
                </div>
            </div>
        </div>
    </div>
</div>