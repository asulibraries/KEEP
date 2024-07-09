KEEP Customizations
===================

This module includes customizations to ASU Libraries' [KEEP](https://keep.lib.asu.edu)
repository. Currently, KEEP-specific customizations are scattered through
various custom modules in our [repositories Drupal instance repo](https://github.com/asulibraries/islandora-repo).
Those customizations will slowly migrate to this repository over time,
but new KEEP-specific customizations will be added here.

## Scholarly Content

This module adds a sidebar, [`scholarly_work_sidebar`](src/Plugin/Block/ScholarlyWorksSidebar.php), for listing the work-products of scholarly content.

We've also added *just enough* config in the `config/optional` directory to
test it on a new site. However, the way media configurations are added, the
file to media mapping for mime and size fields aren't automatically added
and should me manually updated before adding content to test the sidebar.
You will also need to update the Scholarly Content form display to add the
work products field.
