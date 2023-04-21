<?php
    function get_content_core($speaking_type = FALSE, $writing_type = FALSE, $level= FALSE) {
        $lang_speaking_a1_1 = "This performance demonstrates the learner is " . strtolower($speaking_type) . " required to reproduce correctly a limited range of sounds as well as the stress on simple, familiar words and phrases when, for example, reading a text aloud, providing personal information, or talking about such things as likes and dislikes, hobbies and transport.";
        $lang_speaking_a1_2 = "This performance demonstrates the learner is " . strtolower($speaking_type) . " required to reproduce correctly a limited range of sounds as well as the stress on simple, familiar words and phrases when, for example, reading a text aloud, providing personal information, or talking about such things as school/work, lifestyles and photography.";
        $lang_speaking_a1_3 = "This performance demonstrates the learner is " . strtolower($speaking_type) . " required to reproduce correctly a limited range of sounds as well as the stress on simple, familiar words and phrases when, for example, reading a text aloud, providing personal information, or talking about such things as daily routines, entertainment, friends and neighbours.";
        $lang_speaking_a2_1 = "This performance demonstrates the learner is " . strtolower($speaking_type) . " required to generally pronounce English clearly enough to be understood when, for example, reading a text aloud, providing personal information, or talking about such things as their neighbourhood, holidays, study methods (though conversational partners may need to ask for repetition from time to time).";
        $lang_speaking_a2_2 = "This performance demonstrates the learner is " . strtolower($speaking_type) . " required to generally pronounce English clearly enough to be understood when, for example, reading a text aloud, providing personal information, or talking about such things as celebrations, methods of transport (though conversational partners may need to ask for repetition from time to time).";
        $lang_speaking_a2_3 = "This performance demonstrates the learner is " . strtolower($speaking_type) . " required to generally pronounce English clearly enough to be understood when, for example, reading a text aloud, providing personal information, or talking about such things as past experiences, benefits of travel, the importance of exercise (though conversational partners may need to ask for repetition from time to time).";
        $lang_speaking_b1_1 = "This performance demonstrates the learner is " . strtolower($speaking_type) . " required to approximate the intonation and stress of English when talking about such things as employment opportunities, different cultures, the age of responsibility. However, accent may be influenced by other language(s) he/she speaks.";
        $lang_speaking_b1_2 = "This performance demonstrates the learner is " . strtolower($speaking_type) . " required to approximate the intonation and stress of English when talking about such things as local traditions, architectural preservation, the pros and cons of technology. However, accent may be influenced by other language(s) he/she speaks.";
        $lang_speaking_b1_3 = "This performance demonstrates the learner is " . strtolower($speaking_type) . " required to approximate the intonation and stress of English when talking about such things as roles within the family, speculating about the future, the responsibility of advertisers. However, accent may be influenced by other language(s) he/she speaks.";

        $lang_writing_a2_1 = "This performance demonstrates the learner is " . strtolower($writing_type) . " required to write a series of simple phrases and sentences linked with simple connectors like ‘and,’ ‘but’ and 'because' to, for example complete forms and write longer responses to emails and letters.";
        $lang_writing_a2_2 = "This performance demonstrates the learner is " . strtolower($writing_type) . " required to write a series of simple phrases and sentences linked with simple connectors like ‘and,’ ‘but’ and 'because' to, for example write a longer piece of text such as a diary entry or a report on an incident that happened in the past.";
        $lang_writing_a2_3 = "This performance demonstrates the learner is " . strtolower($writing_type) . " required to write a series of simple phrases and sentences linked with simple connectors like ‘and,’ ‘but’ and 'because' to, for example write a longer piece of text such as a review of a product or film, or a descriptive letter to a friend.";
        $lang_writing_b1_1 = "This performance demonstrates the learner is " . strtolower($writing_type) . " required to write straightforward longer texts to give their opinion on a range of subjects, such as current affairs or moving on from school or university, by linking a series of shorter discrete elements into a linear sequence.";
        $lang_writing_b1_2 = "This performance demonstrates the learner is " . strtolower($writing_type) . " required to write straightforward longer texts to, for example, describe positive and negative experiences, and objects and situations in specific detail, by linking a series of shorter discrete elements into a linear sequence.";
        $lang_writing_b1_3 = "This performance demonstrates the learner is " . strtolower($writing_type) . " required to write straightforward longer texts to, for example, express more precisely such things as hypothetical situations, and narrate a story effectively, by linking a series of shorter discrete elements into a linear sequence.";
        if($speaking_type != FALSE){
            $speaking_content = "$"."lang_speaking_".strtolower(str_replace(".","_",$level));
            eval("\$speaking_content = \"$speaking_content\";");
           $lang['speaking'] = $speaking_content; 
        }
        if($writing_type != FALSE){
            $writing_content = "$"."lang_writing_".strtolower(str_replace(".","_",$level));
            eval("\$writing_content = \"$writing_content\";");
            $lang['writing'] = $writing_content; 
        }
         return $lang;
    }

    function get_level_contents($result_status = False, $level = FALSE){
        $lang_extended_a1_1_line1="introduce him/herself and provide personal information.";
        $lang_extended_a1_1_line2="respond to short personal messages, emails, texts, and complete basic forms.";
        $lang_extended_a1_1_line3="describe other people, what they do, and the places they live and work in.";
        $lang_extended_a1_1_line4="make arrangements for social and work-related activities.";
        $lang_extended_a1_2_line1="introduce him/herself and family members, including their occupations and hobbies.";
        $lang_extended_a1_2_line2="deal with different types of personal communication, e.g. short messages, emails, texts, forms and letters";
        $lang_extended_a1_2_line3="describe other people, places, the weather, and past events.";
        $lang_extended_a1_2_line4="ask and answer questions, request help, make arrangements, check information, etc.";
        $lang_extended_a1_3_line1="introduce him/herself and family members, including relationships and extended family histories.";
        $lang_extended_a1_3_line2="deal with a mix of personal and work-related written and audio communications.";
        $lang_extended_a1_3_line3="describe other people, places, experiences, and make comparisons.";
        $lang_extended_a1_3_line4="offer advice on a range of personal and work-related topics, and express and justify opinions.";
        $lang_extended_a2_1_line1="talk about where they live, what they do for a living, their family and plans for the future.";
        $lang_extended_a2_1_line2="complete forms and write longer responses to emails and letters.";
        $lang_extended_a2_1_line3="ask for and offer advice in relation to health, using the internet, customer orders, travel options.";
        $lang_extended_a2_1_line4="read signs in shopping malls and the countryside, ask for directions and make choices.";
        $lang_extended_a2_2_line1="talk in the past, present and future about accommodation, jobs, interests, including likes and dislikes.";
        $lang_extended_a2_2_line2="write a longer piece of text such as a diary entry or a report on an incident that happened in the past.";
        $lang_extended_a2_2_line3="give personal views on topics such as dining out, fashion, online shopping, sport, health.";
        $lang_extended_a2_2_line4="make bookings, for example cinema tickets or travel arrangements, and check information.";
        $lang_extended_a2_3_line1="talk about past experiences, daily routines, and future aspirations in relation to health, the environment, travel etc.";
        $lang_extended_a2_3_line2="write a longer piece of text such as a review of a product or film, or a descriptive letter to a friend.";
        $lang_extended_a2_3_line3="compare such things as products, attitudes to sports, different jobs, accommodation, and give reasons for preferences.";
        $lang_extended_a2_3_line4="describe a condition, such as a health or environmental concern, and ask for an opinion.";
        $lang_extended_b1_1_line1="use a variety of language structures appropriately, including verb tenses, complex prepositions, verbs of feeling and attitude.";
        $lang_extended_b1_1_line2="give opinions and write longer texts on issues such as current affairs, moving on from school or university.";
        $lang_extended_b1_1_line3="process longer texts and audios, such as mini-lectures about scientific research or the Arts, blogs, short stories and TV soaps.";
        $lang_extended_b1_1_line4="use language that is appropriate for dealing with difficult situations, for example customer complaints, explaining a problem to a doctor.";
        $lang_extended_b1_2_line1="use a variety of language structures appropriately, including formal and informal styles of writing.";
        $lang_extended_b1_2_line2="write longer texts to describe positive and negative experiences, and specific details of objects and situations.";
        $lang_extended_b1_2_line3="recognise different points of view, from a range of personal and work-related scenarios, and draw conclusions.";
        $lang_extended_b1_2_line4="use word and sentence stress to convey strong opinions and emotions and sympathetic messages.";
        $lang_extended_b1_3_line1="use more complex grammatical structures such as relative and adverbial clauses, question tags, and the passive voice.";
        $lang_extended_b1_3_line2="express more precisely such things as hypothetical situations, and narrate a story effectively.";
        $lang_extended_b1_3_line3="process longer texts and audios on abstract topics such as travel etiquette, natural phenomena, crime and punishment.";
        $lang_extended_b1_3_line4="communicate using the conventions of conversational communication and effective pronunciation, and when to use direct and indirect speech.";

        $arrays = ['A1.1','A1.2','A1.3','A2.1','A2.2','A2.3','B1.1','B1.2','B1.3'];
        if($result_status != False && $result_status == "Pass"){
            foreach ($arrays as $array){
                $level_new = str_replace(".","_",$array);
                for ($count = 1; $count <= 4; $count++) {
                    $result_cefr_all = "$"."lang_extended_".strtolower($level_new)."_line".$count;
                    eval("\$result_cefr_all = \"$result_cefr_all\";");
                    $lang['result_cefr_content_all'][$array][] = $result_cefr_all; 
                }
            }
            return $lang['result_cefr_content_all'];
        }
    
        if($level != False){
            if(in_array($level, $arrays)){
                $level_new = str_replace(".","_",$level);
                    for ($count = 1; $count <= 4; $count++) {
                        $result_cefr = "$"."lang_extended_".strtolower($level_new)."_line".$count;
                        eval("\$result_cefr = \"$result_cefr\";");
                        $lang['result_cefr_content'][] = $result_cefr; 
                    }
                    return $lang['result_cefr_content'];
                }
        }
    }
?>