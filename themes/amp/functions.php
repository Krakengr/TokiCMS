<?php

function AmpSocialMenu()
{
	$CurrentLang = CurrentLang();
	
	$code = $CurrentLang['lang']['code'];
	
	$socialData = $CurrentLang['data']['social'];
	
	if ( empty( $socialData ) )
		return null;

	$count = $i = 0;
	
	foreach ( $socialData as $id => $social )
	{
		if ( empty( $social ) )
			continue;
		
		$count++;
		
	}
	
	if ( $count == 0 )
		return;
		  
	$html = '<ul class="ampstart-social-follow list-reset flex justify-around items-center flex-wrap m0 mb4">';
	
	foreach ( $socialData as $id => $social )
	{
		if ( empty( $social ) )
			continue;
		
		$i++;

		$html .= '<li>
              <a
                href="' . $social . '"
                target="_blank"
                class="inline-block p1"
                aria-label="Visit us on ' . ucfirst( $id ) . '"
                ><svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 54 54"
                >
                  <title>' . ucfirst( $id ) . '</title>
                  <path
                    d="M47.5 43c0 1.2-.9 2.1-2.1 2.1h-10V30h5.1l.8-5.9h-5.9v-3.7c0-1.7.5-2.9 3-2.9h3.1v-5.3c-.6 0-2.4-.2-4.6-.2-4.5 0-7.5 2.7-7.5 7.8v4.3h-5.1V30h5.1v15.1H10.7c-1.2 0-2.2-.9-2.2-2.1V8.3c0-1.2 1-2.2 2.2-2.2h34.7c1.2 0 2.1 1 2.1 2.2V43"
                    class="ampstart-icon ampstart-icon-' . $id . '"
                  ></path></svg
              ></a>
            </li>';
	}
	
	$html .= '</ul>';

	echo $html;
}
