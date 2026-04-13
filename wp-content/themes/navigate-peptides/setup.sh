#!/bin/bash
# ==========================================================================
# Navigate Peptides — WordPress Setup Script
# Run this ONCE after installing WordPress + WooCommerce + activating the theme.
#
# Usage:
#   cd /path/to/wordpress
#   bash wp-content/themes/navigate-peptides/setup.sh
#
# Prerequisites:
#   - WordPress installed and configured
#   - WooCommerce plugin installed and activated
#   - Navigate Peptides theme activated
#   - WP-CLI installed (https://wp-cli.org)
# ==========================================================================

set -e

echo "=========================================="
echo " Navigate Peptides — Site Setup"
echo "=========================================="

# Check WP-CLI
if ! command -v wp &> /dev/null; then
    echo "ERROR: WP-CLI is required. Install from https://wp-cli.org"
    exit 1
fi

# Check WordPress
if ! wp core is-installed 2>/dev/null; then
    echo "ERROR: WordPress is not installed. Run this from the WordPress root directory."
    exit 1
fi

echo ""
echo "[1/7] Setting up basic site options..."
wp option update blogname "Navigate Peptides"
wp option update blogdescription "Research Peptide Compounds"
wp option update timezone_string "America/New_York"
wp option update date_format "F j, Y"
wp option update permalink_structure "/%postname%/"
wp rewrite flush

echo ""
echo "[2/7] Creating pages and assigning templates..."

# Homepage
HOME_ID=$(wp post create --post_type=page --post_title="Home" --post_status=publish --porcelain)
wp option update page_on_front "$HOME_ID"
wp option update show_on_front "page"

# Compounds (redirects to WooCommerce shop)
COMPOUNDS_ID=$(wp post create --post_type=page --post_title="Compounds" --post_name="compounds" --post_status=publish --porcelain)
wp post meta update "$COMPOUNDS_ID" _wp_page_template "page-templates/template-compounds.php"

# Research Hub
RESEARCH_ID=$(wp post create --post_type=page --post_title="Research" --post_name="research" --post_status=publish --porcelain)
wp post meta update "$RESEARCH_ID" _wp_page_template "page-templates/template-research.php"

# Quality pages
QUALITY_ID=$(wp post create --post_type=page --post_title="Quality" --post_name="quality" --post_status=publish --porcelain)
wp post meta update "$QUALITY_ID" _wp_page_template "page-templates/template-quality.php"

QUALITY_TESTING_ID=$(wp post create --post_type=page --post_title="Testing & Verification" --post_name="testing" --post_status=publish --post_parent="$QUALITY_ID" --porcelain)
wp post meta update "$QUALITY_TESTING_ID" _wp_page_template "page-templates/template-quality-testing.php"

QUALITY_COA_ID=$(wp post create --post_type=page --post_title="Lab Results / COA" --post_name="coa" --post_status=publish --post_parent="$QUALITY_ID" --porcelain)
wp post meta update "$QUALITY_COA_ID" _wp_page_template "page-templates/template-quality-coa.php"

QUALITY_MFG_ID=$(wp post create --post_type=page --post_title="Manufacturing Standards" --post_name="manufacturing" --post_status=publish --post_parent="$QUALITY_ID" --porcelain)
wp post meta update "$QUALITY_MFG_ID" _wp_page_template "page-templates/template-quality-manufacturing.php"

QUALITY_HANDLING_ID=$(wp post create --post_type=page --post_title="Handling & Storage" --post_name="handling" --post_status=publish --post_parent="$QUALITY_ID" --porcelain)
wp post meta update "$QUALITY_HANDLING_ID" _wp_page_template "page-templates/template-quality-handling.php"

# About pages
ABOUT_ID=$(wp post create --post_type=page --post_title="About" --post_name="about" --post_status=publish --porcelain)
wp post meta update "$ABOUT_ID" _wp_page_template "page-templates/template-about.php"

STANDARDS_ID=$(wp post create --post_type=page --post_title="Standards" --post_name="standards" --post_status=publish --post_parent="$ABOUT_ID" --porcelain)
wp post meta update "$STANDARDS_ID" _wp_page_template "page-templates/template-about-standards.php"

CONTACT_ID=$(wp post create --post_type=page --post_title="Contact" --post_name="contact" --post_status=publish --post_parent="$ABOUT_ID" --porcelain)
wp post meta update "$CONTACT_ID" _wp_page_template "page-templates/template-contact.php"

echo "  Created 11 pages with templates assigned."

echo ""
echo "[3/7] Creating WooCommerce product categories..."

declare -A CAT_IDS
CATS=("Metabolic Research:metabolic-research" "Tissue Repair Research:tissue-repair-research" "Cognitive Research:cognitive-research" "Inflammation Research:inflammation-research" "Cellular Research:cellular-research" "Dermal Research:dermal-research" "Research Blends:research-blends")

for cat_pair in "${CATS[@]}"; do
    IFS=':' read -r name slug <<< "$cat_pair"
    CID=$(wp wc product_cat create --name="$name" --slug="$slug" --user=1 --porcelain 2>/dev/null || echo "exists")
    if [ "$CID" != "exists" ]; then
        CAT_IDS[$slug]=$CID
        echo "  Created: $name ($slug) → ID $CID"
    else
        CID=$(wp wc product_cat list --slug="$slug" --field=id --user=1 2>/dev/null)
        CAT_IDS[$slug]=$CID
        echo "  Exists:  $name ($slug) → ID $CID"
    fi
done

echo ""
echo "[4/7] Creating sample products..."

create_product() {
    local name="$1"
    local slug="$2"
    local cat_id="$3"
    local price="$4"
    local subtitle="$5"
    local desc="$6"
    local cas="$7"
    local mw="$8"
    local sequence="$9"
    local purity="${10}"
    local focus="${11}"

    PID=$(wp wc product create \
        --name="$name" \
        --slug="$slug" \
        --type=simple \
        --regular_price="$price" \
        --short_description="$desc" \
        --description="$desc" \
        --categories="[{\"id\":$cat_id}]" \
        --status=publish \
        --catalog_visibility=visible \
        --user=1 \
        --porcelain 2>/dev/null)

    if [ -n "$PID" ]; then
        wp post meta update "$PID" _nav_technical_subtitle "$subtitle"
        wp post meta update "$PID" _nav_cas_number "$cas"
        wp post meta update "$PID" _nav_molecular_weight "$mw"
        wp post meta update "$PID" _nav_sequence "$sequence"
        wp post meta update "$PID" _nav_purity "$purity"
        wp post meta update "$PID" _nav_form "Lyophilized powder"
        wp post meta update "$PID" _nav_storage "-20°C. Protect from light and moisture."
        wp post meta update "$PID" _nav_research_focus "$focus"
        echo "  Created: $name → ID $PID"
    fi
}

# Metabolic Research
create_product "AOD-9604" "aod-9604" "${CAT_IDS[metabolic-research]}" "42.99" \
    "Modified hGH Fragment 177-191" \
    "A modified fragment of human growth hormone spanning amino acids 177-191, investigated for its role in lipolytic pathway signaling." \
    "221231-10-3" "1815.1 Da" "hGH fragment 177-191 (modified)" \
    "≥98% (HPLC verified)" \
    "Lipolytic signaling pathways independent of GH receptor
Beta-3 adrenergic receptor interaction studies
Adipocyte differentiation mechanisms"

create_product "CJC-1295 (no DAC)" "cjc-1295-no-dac" "${CAT_IDS[metabolic-research]}" "29.99" \
    "Modified GRF 1-29 — Tetrasubstituted Analog" \
    "A synthetic analog of growth hormone-releasing hormone (GHRH 1-29) with four amino acid substitutions for enhanced receptor binding stability." \
    "863288-34-0" "3367.9 Da" "Modified GHRH(1-29)" \
    "≥98% (HPLC verified)" \
    "GHRH receptor binding affinity and signaling cascade
GH/IGF-1 axis regulation mechanisms
Pulsatile GH secretion pathway research"

create_product "Ipamorelin" "ipamorelin" "${CAT_IDS[metabolic-research]}" "29.99" \
    "Selective GH Secretagogue — Pentapeptide" \
    "A synthetic pentapeptide growth hormone secretagogue studied for its selective activation of GH release through the ghrelin receptor pathway." \
    "170851-70-4" "711.9 Da" "Aib-His-D-2Nal-D-Phe-Lys-NH2" \
    "≥98% (HPLC verified)" \
    "Selective ghrelin receptor agonism mechanisms
GH secretion pattern analysis
Minimal effect on cortisol and prolactin pathways"

# Tissue Repair Research
create_product "BPC-157" "bpc-157" "${CAT_IDS[tissue-repair-research]}" "39.99" \
    "Synthetic Pentadecapeptide — BPC Fragment 15" \
    "A pentadecapeptide derived from human gastric juice, studied for its involvement in angiogenic and tissue-remodeling signaling pathways." \
    "137525-51-0" "1419.5 Da" "Gly-Glu-Pro-Pro-Pro-Gly-Lys-Pro-Ala-Asp-Asp-Ala-Gly-Leu-Val" \
    "≥99% (HPLC verified)" \
    "VEGF pathway upregulation mechanisms
Nitric oxide system modulation
Growth factor receptor signaling
Extracellular matrix remodeling pathways"

create_product "TB-500" "tb-500" "${CAT_IDS[tissue-repair-research]}" "34.99" \
    "Thymosin Beta-4 Fragment — Actin-Binding Peptide" \
    "A synthetic peptide fragment of thymosin beta-4, investigated for its role in actin regulation and cellular migration signaling." \
    "77591-33-4" "4963.5 Da" "Thymosin Beta-4 (1-43)" \
    "≥98% (HPLC verified)" \
    "Actin polymerization and cytoskeletal dynamics
Cell migration and motility mechanisms
Inflammatory mediator signaling"

# Cognitive Research
create_product "Selank" "selank" "${CAT_IDS[cognitive-research]}" "34.99" \
    "Synthetic Tuftsin Analog — Heptapeptide" \
    "A synthetic heptapeptide analog of the immunomodulatory peptide tuftsin, studied for its interaction with GABAergic and monoamine neurotransmitter systems." \
    "129954-34-3" "751.9 Da" "Thr-Lys-Pro-Arg-Pro-Gly-Pro" \
    "≥98% (HPLC verified)" \
    "GABA receptor modulation mechanisms
BDNF expression pathway analysis
Monoamine neurotransmitter system interactions"

create_product "Semax" "semax" "${CAT_IDS[cognitive-research]}" "34.99" \
    "Synthetic ACTH(4-7) Analog — Heptapeptide" \
    "A synthetic peptide derived from adrenocorticotropic hormone fragment 4-10, investigated for neurotrophic factor signaling pathways." \
    "80714-61-0" "813.9 Da" "Met-Glu-His-Phe-Pro-Gly-Pro" \
    "≥98% (HPLC verified)" \
    "BDNF and NGF expression modulation
Dopaminergic and serotonergic system interactions
Neurotrophic signaling cascade analysis"

# Inflammation Research
create_product "KPV" "kpv" "${CAT_IDS[inflammation-research]}" "44.99" \
    "Alpha-MSH C-Terminal Tripeptide Fragment" \
    "The C-terminal tripeptide of alpha-melanocyte stimulating hormone, studied for its interaction with NF-kB and inflammatory cytokine signaling." \
    "67727-97-3" "357.4 Da" "Lys-Pro-Val" \
    "≥98% (HPLC verified)" \
    "NF-kB pathway modulation mechanisms
Pro-inflammatory cytokine signaling regulation
MC1R-independent anti-inflammatory signaling"

# Cellular Research
create_product "Epithalon" "epithalon" "${CAT_IDS[cellular-research]}" "39.99" \
    "Synthetic Tetrapeptide — Ala-Glu-Asp-Gly" \
    "A synthetic tetrapeptide studied for its interaction with telomerase reverse transcriptase and cellular senescence pathways." \
    "307297-39-8" "390.3 Da" "Ala-Glu-Asp-Gly" \
    "≥98% (HPLC verified)" \
    "Telomerase activation mechanisms
Telomere length maintenance pathways
Cellular senescence signaling"

# Dermal Research
create_product "GHK-Cu" "ghk-cu" "${CAT_IDS[dermal-research]}" "39.99" \
    "Copper Tripeptide Complex — GHK-Cu Chelate" \
    "A naturally occurring copper-binding tripeptide investigated for its role in extracellular matrix remodeling and metalloproteinase regulation." \
    "49557-75-7" "403.9 Da" "Gly-His-Lys:Cu²⁺" \
    "≥98% (HPLC verified)" \
    "Collagen and elastin synthesis pathway activation
Matrix metalloproteinase regulation
Fibroblast proliferation signaling
Antioxidant enzyme expression modulation"

# Research Blends
create_product "BPC-157 + TB-500 Blend" "bpc-157-tb-500-blend" "${CAT_IDS[research-blends]}" "64.99" \
    "Dual Peptide Research Formulation" \
    "A combination formulation containing BPC-157 and TB-500 designed for investigating synergistic tissue-remodeling pathway interactions." \
    "" "" "" \
    "≥98% each compound (HPLC verified)" \
    "Synergistic VEGF and actin signaling interactions
Complementary growth factor receptor activation
Combined extracellular matrix remodeling mechanisms"

echo "  Created 11 sample products."

echo ""
echo "[5/7] Creating navigation menu..."

MENU_ID=$(wp menu create "Primary Navigation" --porcelain)
wp menu location assign "$MENU_ID" primary

# Menu items with dropdowns
COMPOUNDS_MENU=$(wp menu item add-post-type "$MENU_ID" "$COMPOUNDS_ID" --title="Compounds" --porcelain)
for cat_pair in "${CATS[@]}"; do
    IFS=':' read -r name slug <<< "$cat_pair"
    CID="${CAT_IDS[$slug]}"
    if [ -n "$CID" ]; then
        wp menu item add-custom "$MENU_ID" "$name" "$(wp option get siteurl)/product-category/$slug/" --parent-id="$COMPOUNDS_MENU" 2>/dev/null
    fi
done

QUALITY_MENU=$(wp menu item add-post-type "$MENU_ID" "$QUALITY_ID" --title="Quality" --porcelain)
wp menu item add-post-type "$MENU_ID" "$QUALITY_TESTING_ID" --title="Testing & Verification" --parent-id="$QUALITY_MENU"
wp menu item add-post-type "$MENU_ID" "$QUALITY_COA_ID" --title="Lab Results / COA" --parent-id="$QUALITY_MENU"
wp menu item add-post-type "$MENU_ID" "$QUALITY_MFG_ID" --title="Manufacturing Standards" --parent-id="$QUALITY_MENU"
wp menu item add-post-type "$MENU_ID" "$QUALITY_HANDLING_ID" --title="Handling & Storage" --parent-id="$QUALITY_MENU"

RESEARCH_MENU=$(wp menu item add-post-type "$MENU_ID" "$RESEARCH_ID" --title="Research" --porcelain)

ABOUT_MENU=$(wp menu item add-post-type "$MENU_ID" "$ABOUT_ID" --title="About" --porcelain)
wp menu item add-post-type "$MENU_ID" "$STANDARDS_ID" --title="Standards" --parent-id="$ABOUT_MENU"
wp menu item add-post-type "$MENU_ID" "$CONTACT_ID" --title="Contact" --parent-id="$ABOUT_MENU"

echo "  Created primary navigation with dropdowns."

echo ""
echo "[6/7] Configuring WooCommerce settings..."

# Set shop page
SHOP_ID=$(wp wc shop_order list --format=ids 2>/dev/null | head -1)
wp option update woocommerce_shop_page_id "$COMPOUNDS_ID" 2>/dev/null
wp option update woocommerce_currency "USD"
wp option update woocommerce_currency_pos "left"
wp option update woocommerce_price_thousand_sep ","
wp option update woocommerce_price_decimal_sep "."
wp option update woocommerce_price_num_decimals "2"
wp option update woocommerce_default_country "US:CA"
wp option update woocommerce_calc_taxes "no"
wp option update woocommerce_enable_reviews "no"

echo "  WooCommerce configured."

echo ""
echo "[7/7] Flushing rewrite rules..."
wp rewrite flush

echo ""
echo "=========================================="
echo " Setup complete!"
echo "=========================================="
echo ""
echo " Your site is ready at: $(wp option get siteurl)"
echo ""
echo " Next steps:"
echo "   1. Upload product images in WP Admin → Products"
echo "   2. Upload COA PDFs and add URLs to product fields"
echo "   3. Install AllayPay/NMI plugin for payment processing"
echo "   4. Install Coinbase Commerce plugin for crypto"
echo "   5. Configure shipping zones in WooCommerce → Settings → Shipping"
echo "   6. Run a compliance audit: check every page for RUO disclaimers"
echo "   7. Test checkout flow end-to-end with test mode"
echo ""
