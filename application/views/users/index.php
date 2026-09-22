<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_nav = 'users';
$modal_mode = 'create';
$modal_id = 0;
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Users | Curib Employee</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
	<?php $this->load->view('components/layout.php'); ?>

	<main class="app-main container py-4 py-lg-5">
		<div class="card border-0 bg-primary shadow-lg rounded-4 mb-4">
			<div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
				<div>
					<span class="badge rounded-pill text-light mb-2">User Management</span>
					<h1 class="h2 fw-bold text-light mb-1">Users</h1>
					<p class="text-light mb-0">Manage registered accounts separately from employees.</p>
				</div>
				<button type="button" class="btn btn-light btn-lg rounded-3 px-4 align-self-md-center" data-bs-toggle="modal" data-bs-target="#userModal" data-user-mode="create">Register User</button>
			</div>
		</div>

		<section class="card border-0 shadow-lg rounded-4 overflow-hidden">
			<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 p-4 border-bottom bg-white">
				<div><h2 class="h5 mb-1">User Directory</h2></div>
				<span class="badge rounded-pill text-bg-primary-subtle text-primary-emphasis px-3 py-2"><?= (int) $user_total; ?> <?= $user_total === 1 ? 'user' : 'users'; ?></span>
			</div>

			<form method="get" action="<?= html_escape(site_url('users')); ?>" class="p-3 border-bottom bg-body-tertiary" data-auto-filter>
				<input type="hidden" name="per_page" value="<?= (int) $user_per_page; ?>">
				<div class="row g-2 align-items-end">
					<div class="col-md-10">
						<label class="form-label small" for="userSearch">Search users</label>
						<input class="form-control" id="userSearch" name="search" value="<?= html_escape($user_search); ?>" placeholder="Name, email, address, or contact number">
					</div>
					<div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Search</button></div>
					<div class="col-6 col-md-4"><label class="form-label small" for="userAgeRange">Age range</label><select class="form-select" id="userAgeRange" name="age_range"><option value="">All ages</option><option value="under_18" <?= $user_age_range === 'under_18' ? 'selected' : ''; ?>>Under 18</option><option value="18_30" <?= $user_age_range === '18_30' ? 'selected' : ''; ?>>18-30</option><option value="31_40" <?= $user_age_range === '31_40' ? 'selected' : ''; ?>>31-40</option><option value="41_50" <?= $user_age_range === '41_50' ? 'selected' : ''; ?>>41-50</option><option value="51_plus" <?= $user_age_range === '51_plus' ? 'selected' : ''; ?>>51+</option></select></div>
					<div class="col-6 col-md-4"><label class="form-label small" for="userSort">Sort by name</label><select class="form-select" id="userSort" name="sort"><option value="lastname" <?= $user_sort === 'lastname' ? 'selected' : ''; ?>>Last name</option><option value="firstname" <?= $user_sort === 'firstname' ? 'selected' : ''; ?>>First name</option><option value="birthday" <?= $user_sort === 'birthday' ? 'selected' : ''; ?>>Birthday</option><option value="created_at" <?= $user_sort === 'created_at' ? 'selected' : ''; ?>>Registered date</option></select></div>
					<div class="col-6 col-md-4"><label class="form-label small" for="userDirection">Order</label><select class="form-select" id="userDirection" name="direction"><option value="ASC" <?= $user_direction === 'ASC' ? 'selected' : ''; ?>>Ascending</option><option value="DESC" <?= $user_direction === 'DESC' ? 'selected' : ''; ?>>Descending</option></select></div>
				</div>
			</form>

			<?php if (empty($users)): ?>
				<div class="card-body py-5 text-center">
					<h3 class="h5">No users yet</h3>
					<p class="text-secondary mb-3">Register the first user account to begin.</p>
					<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" data-user-mode="create">Register User</button>
				</div>
			<?php else: ?>
				<div class="table-responsive">
					<table class="table table-hover table-group-divider align-middle mb-0">
						<thead class="table-light text-uppercase small">
							<tr>
								<th scope="col">First Name</th>
								<th scope="col">Last Name</th>
								<th scope="col">Email</th>
								<th scope="col">Birthday</th>
								<th scope="col">Address</th>
								<th scope="col">Registered</th>
								<th scope="col" class="text-end">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($users as $user): ?>
								<?php $is_current_user = (int) $user['Id'] === (int) $current_user['id']; ?>
								<tr class="<?= $is_current_user ? 'table-primary border-primary' : ''; ?>">
									<td class="fw-semibold"><span class="d-inline-flex align-items-center gap-2"><?= html_escape($user['firstname']); ?><?php if ($is_current_user): ?><span class="badge text-bg-primary rounded-pill">You</span><?php endif; ?></span></td>
									<td><?= html_escape($user['lastname']); ?></td>
									<td class="text-break"><?= html_escape($user['email']); ?></td>
									<td><?= html_escape(date('M d, Y', strtotime($user['birthday']))); ?></td>
									<td class="text-secondary text-break"><?= html_escape($user['address']); ?></td>
									<td class="text-nowrap"><?= html_escape(date('M d, Y', strtotime($user['created_at']))); ?></td>
									<td class="text-end">
										<div class="d-flex flex-wrap gap-1 justify-content-end" role="group" aria-label="User actions">
											<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#userInfoModal<?= (int) $user['Id']; ?>">View</button>
											<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#userModal" data-user-mode="edit" data-user-id="<?= (int) $user['Id']; ?>" data-user-firstname="<?= html_escape($user['firstname']); ?>" data-user-lastname="<?= html_escape($user['lastname']); ?>" data-user-birthday="<?= html_escape($user['birthday']); ?>" data-user-address="<?= html_escape($user['address']); ?>" data-user-contact="<?= html_escape($user['contactno']); ?>" data-user-email="<?= html_escape($user['email']); ?>">Edit</button>
											<?php if (!$is_current_user): ?>
												<?= form_open('users/delete/' . (int) $user['Id'], array('class' => 'd-inline', 'onsubmit' => "return confirm('Delete this user?');")); ?>
													<button class="btn btn-sm btn-outline-danger rounded-pill px-3" type="submit">Delete</button>
												<?= form_close(); ?>
											<?php else: ?><span class="badge text-bg-primary-subtle text-primary-emphasis rounded-pill">Current user</span><?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php foreach ($users as $user): ?>
					<div class="modal fade" id="userInfoModal<?= (int) $user['Id']; ?>" tabindex="-1" aria-labelledby="userInfoModalLabel<?= (int) $user['Id']; ?>" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden"><div class="modal-header bg-body-tertiary"><h2 class="modal-title fs-5" id="userInfoModalLabel<?= (int) $user['Id']; ?>">User Information</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body p-4"><dl class="row mb-0"><dt class="col-sm-4 text-secondary">Name</dt><dd class="col-sm-8"><?= html_escape($user['firstname'] . ' ' . $user['lastname']); ?></dd><dt class="col-sm-4 text-secondary">Email</dt><dd class="col-sm-8 text-break"><?= html_escape($user['email']); ?></dd><dt class="col-sm-4 text-secondary">Birthday</dt><dd class="col-sm-8"><?= html_escape(date('M d, Y', strtotime($user['birthday']))); ?></dd><dt class="col-sm-4 text-secondary">Address</dt><dd class="col-sm-8 text-break"><?= html_escape($user['address']); ?></dd><dt class="col-sm-4 text-secondary">Contact</dt><dd class="col-sm-8"><?= html_escape($user['contactno']); ?></dd></dl></div><div class="modal-footer bg-body-tertiary"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button></div></div></div>
					</div>
				<?php endforeach; ?>
				<?php $user_start = $user_total > 0 ? (($user_page - 1) * $user_per_page) + 1 : 0; $user_end = min($user_page * $user_per_page, $user_total); $user_prev_page = max(1, $user_page - 1); $user_next_page = min($user_total_pages, $user_page + 1); ?>
				<div class="p-3 border-top bg-white">
					<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
						<small class="text-secondary">Showing <?= $user_start; ?>-<?= $user_end; ?> of <?= (int) $user_total; ?> users</small>
						<?php if ($user_total_pages > 1): ?>
							<nav aria-label="User pages" class="d-flex align-items-center gap-2">
								<a class="btn btn-sm btn-outline-secondary rounded-circle <?= $user_page <= 1 ? 'disabled' : ''; ?>" href="<?= $user_page <= 1 ? '#' : html_escape(site_url('users') . '?' . http_build_query(array('search' => $user_search, 'age_range' => $user_age_range, 'sort' => $user_sort, 'direction' => $user_direction, 'page' => $user_prev_page, 'per_page' => $user_per_page))); ?>" aria-label="Previous page" <?= $user_page <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>←</a>
								<span class="small fw-semibold text-secondary">Page <?= (int) $user_page; ?> of <?= (int) $user_total_pages; ?></span>
								<a class="btn btn-sm btn-outline-secondary rounded-circle <?= $user_page >= $user_total_pages ? 'disabled' : ''; ?>" href="<?= $user_page >= $user_total_pages ? '#' : html_escape(site_url('users') . '?' . http_build_query(array('search' => $user_search, 'age_range' => $user_age_range, 'sort' => $user_sort, 'direction' => $user_direction, 'page' => $user_next_page, 'per_page' => $user_per_page))); ?>" aria-label="Next page" <?= $user_page >= $user_total_pages ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>→</a>
							</nav>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</section>
	</main>

	<?php $this->load->view('components/modals/user.php'); ?>
	<?php $this->load->view('components/modals/alert.php'); ?>
	<?php $this->load->view('components/modals/logout.php'); ?>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="<?= html_escape(base_url('assets/js/app.js')); ?>"></script>
	<script src="<?= html_escape(base_url('assets/js/users.js')); ?>"></script>
</body>
</html>
