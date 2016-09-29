use strict;
use warnings;

use DBI;

my (@data, $regID, $newRegID);

my $oldDbConn = DBI->connect("DBI:mysql:rites2016:ustwpres404", "root", "vepru6Wa") or die;
my $newDbConn = DBI->connect("DBI:mysql:rowan_ritesregistration:ustwpres404", "root", "vepru6Wa", {AutoCommit => 0}) or die;

my $select = $oldDbConn->prepare("SELECT * FROM registration");
my $insert = $newDbConn->prepare("INSERT INTO registration (firstName, lastName, address, city, state, zipcode, country, bestPhone, secondPhone, email, housing, group_name, otherNames, paymentType, schfund, amountDue, calculatedAmount, amountPaid, regDate, speaker, vendor, staff, notes, noticeDate, noticeCount, guardian, needs, housingGender, noisePreference, substance, coupon, optionCode, transactionId, earlybird, emailList, oldRegId, qualifications, eventCode, eventYear, guid) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ");


my %mapTable;
my $cnt = 0;

my $result = $select->execute();
while ( @data = $select->fetchrow_array() )
{
	$regID = shift(@data);
	print "regID=$regID ", $data[0],' ',$data[1],' ',$data[2], "\n";
	$insert->execute(@data, "xx");
	$newRegID = $newDbConn->last_insert_id( undef, undef, undef, undef );
	#print "Created New Entry regID=$newRegID\n";
	$mapTable{$regID} = $newRegID;
	#last if (++$cnt > 5);
}


$select = $oldDbConn->prepare("SELECT * FROM person");
$insert = $newDbConn->prepare("INSERT INTO person (regID, idx, firstName, lastName, akaName, member, gender, firstros, mealPlan, veggie, age, waiverSigned, birthDate, workshift, ride_need, ride_provide, ride_info, family, quiet, allfemale, allmale, substance, latenight, Clan0, Clan_Msg0, Clan1, Clan_Msg1, Clan2, Clan_Msg2, Clan3, Clan_Msg3, howmanytimes, lasttime, howlong, training, fridayBuffet, saturdayLunch, sundayBrunch, skipFeast, masque, oldPersonId, ritualSupport, kitchenSetup, postCleanup, grove, AssignedClan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


$result = $select->execute();
$cnt = 0;
while ( @data = $select->fetchrow_array() )
{
	my $personId = shift(@data);
	$regID = shift(@data);
	print $data[0],' ',$data[1],' ',$data[2], "\n";
	$newRegID = $mapTable{$regID};
	$insert->execute($newRegID, @data, '');
	#last if (++$cnt > 5);
}





$newDbConn->rollback;

for $regID (sort keys %mapTable)
{
	print "$regID => ", $mapTable{$regID}, "\n";
}
