<?php
/**
 * Leadership — client-side pagination for a leader grid.
 *
 * Empty shell: the [data-leader-paginate] module in main.js fills it with the
 * shared `.deon-pager` control (deonBuildPager), so Board and Key Management
 * page exactly like the Project Gallery and the Knowledge Hub. Starts hidden
 * inline and is revealed by JS only when there is more than one page.
 *
 * Was a round prev/next pair with a "1 / 3" counter — replaced by the shared
 * numbered control (client request 2026-08).
 *
 * @package Deon_Energy
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="pt-4">
	<nav class="deon-pager" data-leader-pagination style="display:none"
	     aria-label="<?php esc_attr_e( 'Team pagination', 'deon-energy' ); ?>"></nav>
</div>
