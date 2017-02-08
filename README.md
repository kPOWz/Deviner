#GUS


##Style Guide

http://kpowz.github.io/gus-bootstrap/
source: https://github.com/kPOWz/gus-bootstrap


##Dependency Management

Composer


##Deployment

To deploy GUS each environment has a bare repo to <code> git push</code> to. The bare repo, having no working copy of the source, is equipped with a post-recieve hook listening for pushes.  When the push completes, the post-recieve hook issues a <code>git pull</code> latest on behalf of the deployment directory.

<table>
	<tr>
		<th>env</th>
		<th>location</th>
		<th>remote</th> 
	</tr>
	<tr>
		<td>test</td>
		<td>testgus.87c.us</td>
		<td>/home/eight7cu/repos/testgus.git</td> 
	</tr>
	<tr>
		<td>live</td>
		<td>gus1pt0.87c.us</td>
		<td>/home/eight7cu/repos/gus1pt0.git</td> 
	</tr>
</table>
