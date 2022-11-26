<?php
	use Carbon_Fields\Container;
	use Carbon_Fields\Field;

	add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options' );
	function crb_attach_theme_options() {
	    Container::make( 'theme_options', __( 'Theme Options' ) )
	        ->add_fields( array(
	            Field::make( 'text', 'crb_copyright', 'Copyright' ),
	        ) );
	}

	add_action( 'carbon_fields_register_fields', 'crb_attach_page_options' );
	function crb_attach_page_options() {
	    Container::make( 'post_meta', __( 'Section Options', 'crb' ) )
	        ->where( 'post_type', '=', 'page' )
	        ->where( 'post_template', '=', 'template-section-based.php' )
	        ->add_fields( array(
	            Field::make( 'complex', 'crb_sections', 'Sections' )
	                ->add_fields( 'text', 'Text', array(
	                    Field::make( 'rich_text', 'text', 'Text' ),
	                ) )
	                ->add_fields( 'text_left_img_2_links', 'Text, left image, 2 links', array(
                		Field::make( 'text', 'title', 'Title' ),
                		Field::make( 'rich_text', 'text', 'Text' ),
                		Field::make( 'text', 'btn_1_text', 'Button 1 text' ),
                		Field::make( 'text', 'btn_2_text', 'Button 2 text' ),
                		Field::make( 'text', 'btn_1_url', 'Button 1 URL' ),
                		Field::make( 'text', 'btn_2_url', 'Button 2 URL' ),
                		Field::make( 'image', 'left_image', 'Left image' ),
	                ) )
	                ->add_fields( 'frontpage_hero', 'Frontpage hero', array(
			            Field::make( 'complex', 'crb_slides', 'Slides' )
			                ->set_layout( 'tabbed-horizontal' )
			                ->add_fields( array(
			                    Field::make( 'image', 'image', 'Image' ),
			                ) )
			        ))
			        ->add_fields( 'link_to_game', 'Link to game button', array(

			        ))
	        ) );
	}

	add_action( 'carbon_fields_register_fields', 'crb_attach_country_options' );
	function crb_attach_country_options() {
	    Container::make( 'post_meta', __( 'Section Options', 'crb' ) )
	        ->where( 'post_type', '=', 'country' )
	        ->add_fields( array(
                Field::make( 'text', 'population', 'Population' ),
                Field::make( 'complex', 'parties', 'Parties' )
	                ->set_layout( 'tabbed-horizontal' )
	                ->add_fields( array(
	                    Field::make( 'image', 'logo', 'Logo' ),
	                    Field::make( 'text', 'name', 'Name' ),
	                    Field::make( 'text', 'leader', 'Leader' ),
	                    Field::make( 'text', 'number_of_seats', 'Number of seats' ),
	                    Field::make( 'text', 'gallup_percentage', 'Gallup percentage' ),
	                    Field::make( 'text', 'last_election_share', 'Last election share of vote' ),
	                   	Field::make( 'color', 'colour', 'Colour' ),
	                   	Field::make( 'text', 'goverment', 'Is in the goverment coalition? true/false' ),
	                   	Field::make( 'association', 'primary_ideology', 'Primary ideology' )->set_types( array(
					        array(
					            'type' => 'post',
					            'post_type' => 'ideology',
					        )
				    	) )->set_max( 1 ),
	                    Field::make( 'association', 'ideology', 'Ideology' )->set_types( array(
					        array(
					            'type' => 'post',
					            'post_type' => 'ideology',
					        )
				    	) ),
	                ) ),
                Field::make( 'complex', 'adopted_policies', 'Adopted policies' )
	                ->set_layout( 'tabbed-horizontal' )
	                ->add_fields( array(
	                    Field::make( 'text', 'level', 'Level 1-100' ),
	                   	Field::make( 'association', 'the_policy', 'The Policy' )->set_types( array(
					        array(
					            'type' => 'post',
					            'post_type' => 'policies',
					        )
				    	) )->set_max( 1 ),
	                ) ),
                Field::make( 'complex', 'adopted_taxes', 'Adopted taxes' )
	                ->set_layout( 'tabbed-horizontal' )
	                ->add_fields( array(
	                    Field::make( 'text', 'level', 'Level 1-100' ),
	                   	Field::make( 'association', 'the_tax', 'The Tax' )->set_types( array(
					        array(
					            'type' => 'post',
					            'post_type' => 'taxes',
					        )
				    	) )->set_max( 1 ),
	                ) ),
	            Field::make( 'text', 'gdp', 'GDP (usd)' ),
	            Field::make( 'text', 'debt', 'National debt (usd)' ),
	            Field::make( 'text', 'capital', 'Capital' ),
	            Field::make( 'image', 'flag', 'Flag' ),
	            Field::make( 'text', 'number_of_seats_in_parliament', 'Number of seats in parliament' ),
	            Field::make( 'image', 'hero_banner', 'Hero banner' ),
	            Field::make( 'text', 'currency', 'Currency full name' ),
	           	Field::make( 'text', 'region', 'Region' ),
	           	Field::make( 'text', 'prime_minister', 'Prime Minister' ),
	           	Field::make( 'text', 'employment_rate', 'Employment rate %' ),
	            Field::make( 'text', 'crime_level', 'Crime level (0-100)' ),
	            Field::make( 'text', 'freedom_level', 'Freedom level (0-100)' ),
	            Field::make( 'text', 'civil_rights_level', 'Civil rights level (0-100)' ),
	            Field::make( 'text', 'health_level', 'Health level (0-100)' ),
	            Field::make( 'text', 'tourist_attractiveness_level', 'Tourist attractiveness level (0-100)' ),
	            Field::make( 'text', 'education_level', 'Education level (0-100)' ),
	            Field::make( 'text', 'culture_level', 'Culture level (0-100)' ),
	            Field::make( 'text', 'average_income', 'Average income in the country' ),
	            Field::make( 'text', 'average_income_high', 'Average income of the top 10%' ),
	            Field::make( 'text', 'average_income_low', 'Average income of the bottom 10%' ),
            ) );
	}

	add_action( 'carbon_fields_register_fields', 'crb_attach_randomevent_options' );
	function crb_attach_randomevent_options() {
	    Container::make( 'post_meta', __( 'Section Options', 'crb' ) )
	        ->where( 'post_type', '=', 'randomevents' )
	        ->add_fields( array(
                Field::make( 'text', 'population_change', 'Population change multiplier' ),
	            Field::make( 'text', 'gdp_change', 'GDP change multiplier' ),
                Field::make( 'complex', 'ideology_effects', 'Ideology effects' )
	                ->set_layout( 'tabbed-horizontal' )
	                ->add_fields( array(
			            Field::make( 'association', 'ideologies_add_happiness', 'Ideologiess add popularity' )->set_types( array(
					        array(
					            'type' => 'post',
					            'post_type' => 'ideology',
					        )
				    	) ),
				    	Field::make( 'association', 'ideologies_lose_happiness', 'Ideologiess lose popularity' )->set_types( array(
					        array(
					            'type' => 'post',
					            'post_type' => 'ideology',
					        )
				    	) ),
	                ) ),
	            // New data
	            Field::make( 'text', 'employment_rate_change', 'Employment rate change in %-points' ),
	            Field::make( 'text', 'crime_level_change', 'Crime level (0-100) change in levels' ),
	            Field::make( 'text', 'freedom_level_change', 'Freedom level (0-100) change in levels' ),
	            Field::make( 'text', 'civil_rights_level_change', 'Civil rights level (0-100) change in levels' ),
	            Field::make( 'text', 'health_level_change', 'Health level (0-100) change in levels' ),
	            Field::make( 'text', 'tourist_attractiveness_level_change', 'Tourist attractiveness level (0-100) change in levels' ),
	            Field::make( 'text', 'education_level_change', 'Education level (0-100) change in levels' ),
	            Field::make( 'text', 'culture_level_change', 'Culture level (0-100) change in levels' ),
	            Field::make( 'text', 'average_income_change', 'Average income in the country change multiplier' ),
	            Field::make( 'text', 'average_income_high_change', 'Average income of the top 10% change multiplier' ),
	            Field::make( 'text', 'average_income_low_change', 'Average income of the bottom 10% change multiplier' ),
            ) );
	}

	add_action( 'carbon_fields_register_fields', 'crb_attach_policy_options' );
	function crb_attach_policy_options() {
	    Container::make( 'post_meta', __( 'Section Options', 'crb' ) )
	        ->where( 'post_type', '=', 'policies' )
	        ->add_fields( array(
                Field::make( 'complex', 'ideology_effects', 'Ideology effects' )
	                ->set_layout( 'tabbed-horizontal' )
	                ->add_fields( array(
			            Field::make( 'association', 'ideologies_add_happiness', 'Ideologiess add happiness' )->set_types( array(
					        array(
					            'type' => 'post',
					            'post_type' => 'ideology',
					        )
				    	) ),
				    	Field::make( 'association', 'ideologies_lose_happiness', 'Ideologiess lose happiness' )->set_types( array(
					        array(
					            'type' => 'post',
					            'post_type' => 'ideology',
					        )
				    	) ),
	                ) ),
	            Field::make( 'text', 'gdp_per_turn_effects', 'GDP per turn effects (+/- %)' ),
	           	Field::make( 'text', 'death_rate_per_turn_effects', 'Death rate per turn effects (+/- %)' ),
	           	Field::make( 'text', 'birth_rate_per_turn_effects', 'Birth rate per turn effects (+/- %)' ),
	            Field::make( 'text', 'immigration_rate_per_turn_effects', 'Immigration rate per turn effects (+/- %)' ),
	           	Field::make( 'text', 'emigration_rate_per_turn_effects', 'Emigration rate per turn effects (+/- %)' ),
	            Field::make( 'text', 'policy_budgetary_cost_per_capita', 'Annual policy budgetary cost per capita in USD' ),
	            Field::make( 'text', 'happiness_score_effects', 'Happiness score effects (in score)' ),
	            Field::make( 'text', 'employment_rate_change', 'Employment rate change in %-points' ),
	            Field::make( 'text', 'crime_level_change', 'Crime level (0-100) change in levels' ),
	            Field::make( 'text', 'freedom_level_change', 'Freedom level (0-100) change in levels' ),
	            Field::make( 'text', 'civil_rights_level_change', 'Civil rights level (0-100) change in levels' ),
	            Field::make( 'text', 'health_level_change', 'Health level (0-100) change in levels' ),
	            Field::make( 'text', 'tourist_attractiveness_level_change', 'Tourist attractiveness level (0-100) change in levels' ),
	            Field::make( 'text', 'education_level_change', 'Education level (0-100) change in levels' ),
	            Field::make( 'text', 'culture_level_change', 'Culture level (0-100) change in levels' ),
	            Field::make( 'text', 'average_income_change', 'Average income in the country change multiplier' ),
	            Field::make( 'text', 'average_income_high_change', 'Average income of the top 10% change multiplier' ),
	            Field::make( 'text', 'average_income_low_change', 'Average income of the bottom 10% change multiplier' ),
	            Field::make( 'text', 'inflation_add_per_turn_change', 'Inflation add per turn change' ),
            ) );
	}

	add_action( 'carbon_fields_register_fields', 'crb_attach_tax_options' );
	function crb_attach_tax_options() {
	    Container::make( 'post_meta', __( 'Section Options', 'crb' ) )
	        ->where( 'post_type', '=', 'taxes' )
	        ->add_fields( array(
                Field::make( 'complex', 'ideology_effects', 'Ideology effects' )
	                ->set_layout( 'tabbed-horizontal' )
	                ->add_fields( array(
			            Field::make( 'association', 'ideologies_add_happiness', 'Ideologiess add happiness' )->set_types( array(
					        array(
					            'type' => 'post',
					            'post_type' => 'ideology',
					        )
				    	) ),
				    	Field::make( 'association', 'ideologies_lose_happiness', 'Ideologiess lose happiness' )->set_types( array(
					        array(
					            'type' => 'post',
					            'post_type' => 'ideology',
					        )
				    	) ),
	                ) ),
	            Field::make( 'text', 'gdp_per_turn_effects', 'GDP per turn effects (+/- %)' ),
	            Field::make( 'text', 'death_rate_per_turn_effects', 'Death rate per turn effects (+/- %)' ),
	           	Field::make( 'text', 'birth_rate_per_turn_effects', 'Birth rate per turn effects (+/- %)' ),
	            Field::make( 'text', 'immigration_rate_per_turn_effects', 'Immigration rate per turn effects (+/- %)' ),
	           	Field::make( 'text', 'emigration_rate_per_turn_effects', 'Emigration rate per turn effects (+/- %)' ),
	            Field::make( 'text', 'taxes_of_gdp', 'Max tax as % of GDP' ),
	            Field::make( 'text', 'happiness_score_effects', 'Happiness score effects (in score)' ),
	            Field::make( 'text', 'employment_rate_change', 'Employment rate change in %-points' ),
	            Field::make( 'text', 'crime_level_change', 'Crime level (0-100) change in levels' ),
	            Field::make( 'text', 'freedom_level_change', 'Freedom level (0-100) change in levels' ),
	            Field::make( 'text', 'civil_rights_level_change', 'Civil rights level (0-100) change in levels' ),
	            Field::make( 'text', 'health_level_change', 'Health level (0-100) change in levels' ),
	            Field::make( 'text', 'tourist_attractiveness_level_change', 'Tourist attractiveness level (0-100) change in levels' ),
	            Field::make( 'text', 'education_level_change', 'Education level (0-100) change in levels' ),
	            Field::make( 'text', 'culture_level_change', 'Culture level (0-100) change in levels' ),
	            Field::make( 'text', 'average_income_change', 'Average income in the country change multiplier' ),
	            Field::make( 'text', 'average_income_high_change', 'Average income of the top 10% change multiplier' ),
	            Field::make( 'text', 'average_income_low_change', 'Average income of the bottom 10% change multiplier' ),
	           	Field::make( 'text', 'inflation_add_per_turn_change', 'Inflation add per turn change' ),
            ) );
	}

	add_action( 'carbon_fields_register_fields', 'crb_attach_game_options' );
	function crb_attach_game_options() {
	    Container::make( 'post_meta', __( 'Section Options', 'crb' ) )
	        ->where( 'post_type', '=', 'the_game' )
	        ->add_fields( array(
	           	Field::make( 'text', 'player_name', 'Player name' ),
	           	Field::make( 'text', 'player_email', 'Player email' ),
	           	Field::make( 'text', 'goverment', 'Goverment' ),
	           	Field::make( 'text', 'prime_minister', 'Prime Minister' ),
	           	Field::make( 'text', 'country_instance', 'Country instance' ),
	           	Field::make( 'text', 'policies_instance', 'Policies instance' ),
	           	Field::make( 'text', 'taxes_instance', 'Taxes instance' ),
	           	Field::make( 'text', 'history_instance', 'History instance' ),
	           	Field::make( 'text', 'turn_results', 'Previous turn results' ),
	           	Field::make( 'text', 'turn', 'Turn' ),
	            Field::make( 'text', 'gdp_per_turn_effects', 'GDP per turn effects (+/- %)' ),
	           	Field::make( 'text', 'birth_rate_per_turn_effects', 'Birth rate per turn effects (+/- %)' ),
	            Field::make( 'text', 'death_rate_per_turn_effects', 'Death rate per turn effects (+/- %)' ),
	           	Field::make( 'text', 'immigration_rate_per_turn_effects', 'Immigration rate per turn effects (+/- %)' ),
	            Field::make( 'text', 'emigration_rate_per_turn_effects', 'Emigration rate per turn effects (+/- %)' ),
	           	Field::make( 'text', 'policy_budgetary_cost_per_capita', 'Policy budgetary cost per capita in USD' ),
	           	Field::make( 'text', 'taxes_of_gdp', 'Taxes % of GDP' ),
	           	Field::make( 'text', 'inflation_add_per_turn', 'Inflation add per turn %-points' ),
	           	Field::make( 'text', 'inflation_multiplier_total', 'Inflation multiplier total' ),
	            Field::make( 'text', 'actions_left_this_turn', 'Actions left this turn' ),
	            Field::make( 'text', 'goverment_credit_rating', 'Goverment credit rating' ),
	            Field::make( 'text', 'goverment_debt', 'Goverment debt in USD' ),
	           	Field::make( 'text', 'happiness_score', 'Happiness score' ),
	           	Field::make( 'text', 'player_party_affiliation', 'Player party affiliation' ),
	           	Field::make( 'text', 'turns_until_election', 'Turns until election' ),
	           	Field::make( 'text', 'election_held', 'Election held' ),
	           	Field::make( 'text', 'stats_at_previous_election', 'Stats at previous election' ),
	           	Field::make( 'text', 'stats_at_previous_turn', 'Stats at previous turn' ),
	           	Field::make( 'text', 'test_data', 'Test data' ),
	           	Field::make( 'association', 'country', 'Country' )->set_types( array(
			        array(
			            'type' => 'post',
			            'post_type' => 'country',
			        )
		    	) )->set_max(1),
            ) );
	}

