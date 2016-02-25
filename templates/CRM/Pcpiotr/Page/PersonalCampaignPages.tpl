<div class="help">
  <p>{ts 1=$displayName}This page lists all personal campaign pages that %1 has created.{/ts}</p>
</div>

{if $rows}
<p></p>
<div id="ltype">
  {include file="CRM/common/pager.tpl" location="top"}
  {include file="CRM/common/jsortable.tpl"}
  {strip}
  <table id="options" class="display">
    <thead>
      <tr>
        <th>{ts}Page Title{/ts}</th>
        <th>{ts}Status{/ts}</th>
        <th>{ts}Contribution Page / Event{/ts}</th>
        <th>{ts}No. Of Contributions{/ts}</th>
        <th>{ts}Amount Raised{/ts}</th>
        <th>{ts}Target Amount{/ts}</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      {foreach from=$rows item=row key=key}
      <tr id="row_{$row.id}" class="{$row.class}">
        <td><a href="{crmURL p='civicrm/pcp/info' q="reset=1&id=`$key`" fe='true'}" title="{ts}View Personal Campaign Page{/ts}" target="_blank">{$row.page_title}</a></td>
        <td>{$row.status}</td>
        <td>{$row.contribution_page_event}</td>
        <td>{$row.no_of_contributions}</td>
        <td>{$row.amount_raised}</td>
        <td>{$row.target_amount}</td>
        <td>{$row.action}</td>
      </tr>
      {/foreach}
    </tbody>
  </table>
  {/strip}
</div>
{else}
<div class="messages status no-popup">
  <div class="icon inform-icon"></div>
  {ts 1=$displayName}No personal campaign pages have been created by %1.{/ts}
</div>
{/if}